
import axios from 'axios';
import { SPECIALTY_URL } from '../config/config.js';
import swal from 'sweetalert';
import flatpickr from "flatpickr";
import toastr from "toastr";
import appendError from '../components/function.js';
require('flatpickr/dist/flatpickr.css');
require('toastr/build/toastr.css');

const fp_specialty = flatpickr('.datepicker_specialty', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

$(document).on('click', '.specialty_new', async function (e) {
    e.preventDefault();
    resetForm();
    $('.action').text('Créer spécialité');
})

$(document).on('click', '.editeSpecialty', async function (e) {
    e.preventDefault();
    resetForm();
    $('.specialty_form').attr('make', 'edit');
    const url = $(this).attr('href');
    $(this).val('').addClass('disabled  btn-progress');
    await axios.get(url).then(({ data }) => {
        $(this).val('').removeClass('disabled btn-progress').val('<i class="fas fa-pencil-alt"></i>');
        $('.action').text('Edite spécialité');
        $('.specialty_form').attr('action', url);
        $("#name").val(data.name);
        $("#service_id option").attr("selected", false);//init select
        $("#service_id option[value=" + data.service.id + "]").attr('selected', true);
        fp_specialty._input.value = moment(data.created_at).format("DD MMMM  Y, h:mm");
        $('.specialty_submit').text('Modifier');
    });
});

$(document).on('click', '.specialty_reset', function () {
    resetForm();
})

$(document).on('submit', '.specialty_form', async function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = $(this).attr('make') === 'new' ? SPECIALTY_URL + '/new' : form.attr('action');
    const action = $(this).attr('make') === 'new' ? "création" : "modification";
    const bntSubmit = $(this).attr('make') === 'new' ? "Créer" : "Modifier";
    $(".specialty_submit").val('').addClass('disabled  btn-progress');
    await axios.post(url, formData).then(({ data }) => {
        if (data.action == "success") {
            toastr["success"]("success " + action);
            resetForm();
            $('#tableSpecialty').DataTable().ajax.reload();
        } else {
            toastr["error"]("echec de la " + action);
            appendError(data.errors);
            $(".specialty_submit").val('').removeClass('disabled btn-progress').val(bntSubmit);
        }
    });
});

$(document).on('submit', 'form.specialty_form_delete', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = form.attr('action');
    swal({
        title: 'Êtes-vous sûr?',
        text: 'Voulez vous supprimer cette spécialité !!',
        buttons: true,
        dangerMode: true,
        closeOnConfirm: false,
    })
        .then(async (willDelete) => {
            if (willDelete) {
                form.children(".btnSubmitDeleteSpecialty").val('').addClass('disabled  btn-progress');
                await axios.delete(url, formData).then(({ data }) => {
                    if (data.action == "success") {
                        toastr["success"]("success suppression");
                        resetForm();
                        form.children(".btnSubmitDeleteSpecialty").val('').removeClass('disabled btn-progress').val('<i class="fas fa-trash"></i>');
                        $('#tableSpecialty').DataTable().ajax.reload();
                    } else {
                        toastr["error"]("echec de la suppression");
                    }
                });
            }
        });
});


$('#tableSpecialty').DataTable({
    dom: 'Bfrtip',
    "retrieve": true,
    "ordering": false,
    "pageLength": 5,
    ajax: SPECIALTY_URL + '/findAll',
    columns: [
        {
            title: "#",
            data: "id"
        },
        {
            title: "Désignation",
            data: "name"
        },
        {
            title: "Date Création",
            data: function (data) {
                return moment(data.created_at).format("DD MMMM YYYY");
            }
        },
        {
            title: "Service",
            data: "service.name"
        },
        {
            title: "edit",
            data: function (data) {
                const route = SPECIALTY_URL + '/' + data.id + '/edit';
                return ' <a class="btn btn-info btn-action mr-1 editeSpecialty" href="' + route + '" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fas fa-pencil-alt"></i></a>'
            }
        },
        {
            title: "delete",
            data: function (data) {
                const route = SPECIALTY_URL + '/' + data.id + '/delete';
                return ` <form action="${route}" class="specialty_form_delete" method="POST">
                            <button type="submit" class="btn btn-danger btn-action btnSubmitDeleteSpecialty" data-toggle="tooltip" data-original-title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>`;
            }
        },

    ],
    responsive: true,
});


function resetForm() {
    $(".specialty_submit").val('').removeClass('disabled btn-progress').val('Créer');
    $('.specialty_form :input').removeClass(' is-invalid');
    $('.specialty_form :input').next("ul").remove();
    $('.specialty_form').attr('action', '');
    $('.specialty_form').attr('make', 'new');
    $("#name").val('');
    $(".datepicker").val('');
    $('.specialty_submit').text('Créer');
    $("#service_id option").attr("selected", false);//init select
}

$('#tableSpecialty_wrapper>.dt-buttons').css('display', 'none');


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