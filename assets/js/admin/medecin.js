
import axios from 'axios';
import { MEDECIN_URL } from '../config/config.js';
import swal from 'sweetalert';
import flatpickr from "flatpickr";
import toastr from "toastr";
import appendError from '../components/function.js';
require('flatpickr/dist/flatpickr.css');
require('toastr/build/toastr.css');

const fp_dateNais_medecin = flatpickr('.datepicker_dateNais_medecin', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

const fp_created_at_medecin = flatpickr('.datepicker_created_at_medecin', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

$(document).on('click', '.medecin_new', async function (e) {
    e.preventDefault();
    resetForm();
    $('.action').text('Créer medecin');
    const url = $(this).attr('href');
    await axios.get(url).then(({ data }) => {
        $("#matricule").val(data);
    });
})

$(document).on('click', '.editeMedecin', async function (e) {
    e.preventDefault();
    resetForm();
    $('.medecin_form').attr('make', 'edit');
    const url = $(this).attr('href');
    $(this).val('').addClass('disabled  btn-progress');
    await axios.get(url).then(({ data }) => {
        $(this).val('').removeClass('disabled btn-progress').val('<i class="fas fa-pencil-alt"></i>');
        $(".collapse").addClass('show');
        $("#service_id option").attr("selected", false);//init select
        $('.action').text('Edite medecin');
        $('.medecin_form').attr('action', url);
        $("#matricule").val(data.matricule);
        $("#firstName").val(data.firstName);
        $("#lastName").val(data.lastName);
        fp_dateNais_medecin._input.value = moment(data.dateNais).format("DD MMMM  Y, h:mm");
        $("#service_id option[value=" + data.service_id + "]").attr('selected', true);
        $("#genre option[value=" + data.genre + "]").attr('selected', true);
        $("#address").val(data.address);
        $("#email").val(data.email);
        $("#login").val(data.login);
        $("#tel").val(data.tel);
        $("#CNI").val(data.CNI);
        fp_created_at_medecin._input.value = moment(data.created_at).format("DD MMMM  Y, h:mm");
        $('.medecin_submit').text('Modifier');
    });
});

$(document).on('click', '.medecin_reset', function () {
    $(".collapse").removeClass('show');
    resetForm();
})

$(document).on('submit', '.medecin_form', async function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = $(this).attr('make') === 'new' ? MEDECIN_URL + '/new' : form.attr('action');
    const action = $(this).attr('make') === 'new' ? "création" : "modification";
    const bntSubmit = $(this).attr('make') === 'new' ? "Créer" : "Modifier";
    $(".medecin_submit").val('').addClass('disabled  btn-progress');
    await axios.post(url, formData).then(({ data }) => {
        if (data.action == "success") {
            toastr["success"]("success " + action);
            resetForm();
            $(".collapse").removeClass('show');
            $('#tableMedecin').DataTable().ajax.reload();
        } else {
            $(".medecin_submit").val('').removeClass('disabled btn-progress').val(bntSubmit);
            toastr["error"]("echec de la " + action);
            appendError(data.errors);

        }
    });
});

$(document).on('submit', 'form.medecin_form_delete', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = form.attr('action');
    swal({
        title: 'Êtes-vous sûr?',
        text: 'Voulez vous supprimer ce medecin !!',
        buttons: true,
        dangerMode: true,
        closeOnConfirm: false,
    })
        .then(async (willDelete) => {
            if (willDelete) {
                form.children(".btnSubmitDeleteMedecin").val('').addClass('disabled  btn-progress');
                await axios.delete(url, formData).then(({ data }) => {
                    if (data.action == "success") {
                        toastr["success"]("success suppression");
                        resetForm();
                        form.children(".btnSubmitDeleteMedecin").val('').removeClass('disabled btn-progress').val('<i class="fas fa-trash"></i>');
                        $('#tableMedecin').DataTable().ajax.reload();
                    } else {
                        toastr["error"]("echec de la suppression");
                    }
                });
            }
        });
});


const table = $('#tableMedecin').DataTable({
    dom: 'Bfrtip',
    "retrieve": true,
    "ordering": false,
    "pageLength": 5,
    ajax: MEDECIN_URL + '/findAll',
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
            data: "medecin_id"
        },
        {
            title: "matricule",
            data: "matricule"
        },
        {
            title: "Medecin",
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
            data: "specialty.service.name"
        },
        {
            title: "Specialite",
            data: "specialty.name"
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
                const route_edite = MEDECIN_URL + '/' + data.medecin_id + '/edit';
                const route_delete = MEDECIN_URL + '/' + data.id + '/delete';
                return ` <div style="display: flex;"><a class="btn btn-info btn-action mr-1 editeMedecin" href="${route_edite}" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fas fa-pencil-alt"></i></a>
                    <form action="${route_delete}" class="medecin_form_delete" method="POST"  >
                                    <button type="submit" class="btn btn-danger btn-action btnSubmitDeleteMedecin" data-toggle="tooltip" data-original-title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form> </div>`;
            }
        },
    ],
});



function resetForm() {
    $(".medecin_submit").val('').removeClass('disabled btn-progress').val('Créer');
    $('.medecin_form :input').removeClass(' is-invalid');
    $('.medecin_form :input').next("ul").remove();
    $('.medecin_form').attr('action', '');
    $('.medecin_form').attr('make', 'new');
    $("#name").val('');
    $(".datepicker").val('');
    $('.medecin_submit').text('Créer');
    $("#service_id option").attr("selected", false);//init select
}

$('#tableMedecin_wrapper>.dt-buttons').css('display', 'none');


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