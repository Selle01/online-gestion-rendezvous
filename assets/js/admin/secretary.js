
import axios from 'axios';
import { SECRETARY_URL } from '../config/config.js';
import swal from 'sweetalert';
import flatpickr from "flatpickr";
import toastr from "toastr";
import appendError from '../components/function.js';
require('flatpickr/dist/flatpickr.css');
require('toastr/build/toastr.css');

const fp_dateNais_secretary = flatpickr('.datepicker_dateNais_secretary', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

const fp_created_at_secretary = flatpickr('.datepicker_created_at_secretary', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

$(document).on('click', '.secretary_new', async function (e) {
    e.preventDefault();
    resetForm();
    $('.action').text('Créer secretary');
    const url = $(this).attr('href');
    await axios.get(url).then(({ data }) => {
        $("#matricule").val(data);
    });
})

$(document).on('click', '.editeSecretary', async function (e) {
    e.preventDefault();
    resetForm();
    $('.secretary_form').attr('make', 'edit');
    const url = $(this).attr('href');
    $(this).val('').addClass('disabled  btn-progress');
    await axios.get(url).then(({ data }) => {
        $(this).val('').removeClass('disabled btn-progress').val('<i class="fas fa-pencil-alt"></i>');
        $(".collapse").addClass('show');
        $("#service_id option").attr("selected", false);//init select
        $('.action').text('Edite sécretaire');
        $('.secretary_form').attr('action', url);
        $("#matricule").val(data.matricule);
        $("#firstName").val(data.firstName);
        $("#lastName").val(data.lastName);
        fp_dateNais_secretary._input.value = moment(data.dateNais).format("DD MMMM  Y, h:mm");
        $("#service_id option[value=" + data.service_id + "]").attr('selected', true);
        $("#genre option[value=" + data.genre + "]").attr('selected', true);
        $("#address").val(data.address);
        $("#email").val(data.email);
        $("#login").val(data.login);
        $("#tel").val(data.tel);
        $("#CNI").val(data.CNI);
        fp_created_at_secretary._input.value = moment(data.created_at).format("DD MMMM  Y, h:mm");
        $('.secretary_submit').text('Modifier');
    });
});

$(document).on('click', '.secretary_reset', function () {
    $(".collapse").removeClass('show');
    resetForm();
})

$(document).on('submit', '.secretary_form', async function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = $(this).attr('make') === 'new' ? SECRETARY_URL + '/new' : form.attr('action');
    const action = $(this).attr('make') === 'new' ? "création" : "modification";
    const bntSubmit = $(this).attr('make') === 'new' ? "Créer" : "Modifier";
    $(".secretary_submit").val('').addClass('disabled  btn-progress');
    await axios.post(url, formData).then(({ data }) => {
        if (data.action == "success") {
            toastr["success"]("success " + action);
            resetForm();
            $(".collapse").removeClass('show');
            $('#tableSecretary').DataTable().ajax.reload();
        } else {
            $(".secretary_submit").val('').removeClass('disabled btn-progress').val(bntSubmit);
            toastr["error"]("echec de la " + action);
            appendError(data.errors);
        }
    });
});

$(document).on('submit', 'form.secretary_form_delete', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = form.attr('action');
    swal({
        title: 'Êtes-vous sûr?',
        text: 'Voulez vous supprimer cette sécretaire !!',
        buttons: true,
        dangerMode: true,
        closeOnConfirm: false,
    })
        .then(async (willDelete) => {
            if (willDelete) {
                form.children(".btnSubmitDeleteSecretary").val('').addClass('disabled  btn-progress');
                await axios.delete(url, formData).then(({ data }) => {
                    if (data.action == "success") {
                        toastr["success"]("success suppression");
                        resetForm();
                        form.children(".btnSubmitDeleteSecretary").val('').removeClass('disabled btn-progress').val('<i class="fas fa-trash"></i>');
                        $('#tableSecretary').DataTable().ajax.reload();
                    } else {
                        toastr["error"]("echec de la suppression");
                    }
                });
            }
        });
});


const table = $('#tableSecretary').DataTable({
    dom: 'Bfrtip',
    "retrieve": true,
    "ordering": false,
    "pageLength": 5,
    ajax: SECRETARY_URL + '/findAll',
    aaSorting: [],
    responsive: true,
    columnDefs: [
        {
            responsivePriority: 1,
            targets: 0
        },
        {
            responsivePriority: 2,
            targets: -1
        }
    ],
    columns: [

        {
            title: "#",
            data: "id"
        },
        {
            title: "matricule",
            data: "matricule"
        },
        {
            title: "Secretaire",
            data: function (data) {
                return ` <p class="font-weight-bold mb-0">${data.genre == "HOMME" ? "Mr" : "Mme"} ${data.firstName} ${data.lastName}</p> 
                <p class="text-dark mb-0">${data.email}</p>`;
            }
        },
        {
            title: "Date de Naissance",
            data: function (data) {
                return moment(data.dateNais).format("DD MMMM YYYY");
            }
        },
        {
            title: "Adresse",
            data: "address"
        },
        {
            title: "Infos",
            data: function (data) {
                return `<p class="text-dark mb-0">Tel: ${data.tel}</p>
                <p class="text-dark mb-0">CNI: ${data.CNI}</p>`;
            }
        },
        {
            title: "login",
            data: "login"
        },
        {
            title: "Service",
            data: "service.name"
        },
        {
            title: "Date d'inscription",
            data: function (data) {
                return moment(data.created_at).format("DD MMMM YYYY");
            }
        },
        {
            title: "action",
            data: function (data) {
                const route_edite = SECRETARY_URL + '/' + data.user_id + '/edit';
                const route_delete = SECRETARY_URL + '/' + data.user_id + '/delete';
                return ` <div style="display: flex;"><a class="btn btn-info btn-action mr-1 editeSecretary" href="${route_edite}" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fas fa-pencil-alt"></i></a>
                    <form action="${route_delete}" class="secretary_form_delete" method="POST"  >
                                    <button type="submit" class="btn btn-danger btn-action btnSubmitDeleteSecretary" data-toggle="tooltip" data-original-title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form> </div>`;
            }
        },
    ],
});



function resetForm() {
    $(".secretary_submit").val('').removeClass('disabled btn-progress').val('Créer');
    $('.secretary_form :input').removeClass(' is-invalid');
    $('.secretary_form :input').next("ul").remove();
    $('.secretary_form').attr('action', '');
    $('.secretary_form').attr('make', 'new');
    $("#name").val('');
    $(".datepicker").val('');
    $('.secretary_submit').text('Créer');
    $("#service_id option").attr("selected", false);//init select
}

$('#tableSecretary_wrapper>.dt-buttons').css('display', 'none');


toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-left",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "1000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}