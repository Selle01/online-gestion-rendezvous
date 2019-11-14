
import axios from 'axios';
import { SERVICE_URL } from '../config/config.js';
import swal from 'sweetalert';
import flatpickr from "flatpickr";
import toastr from "toastr";
import appendError from '../components/function.js';
require('flatpickr/dist/flatpickr.css');
require('toastr/build/toastr.css');

const fp_service = flatpickr('.datepicker_service', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});

$(document).on('click', '.service_new', async function (e) {
    e.preventDefault();
    resetForm();
    $('.action').text('Créer service');
})

$(document).on('click', '.editeService', async function (e) {
    e.preventDefault();
    resetForm();
    $('.service_form').attr('make', 'edit');
    const url = $(this).attr('href');
     $(this).val('').addClass('disabled  btn-progress');
    await axios.get(url).then(({ data }) => {
         $(this).val('').removeClass('disabled btn-progress').val('<i class="fas fa-pencil-alt"></i>');
        $('.action').text('Edite service');
        $('.service_form').attr('action', url);
        $("#name").val(data.name);
        fp_service._input.value = moment(data.created_at).format("DD MMMM  Y, h:mm");
        $('.service_submit').text('Modifier');
    });
});

$(document).on('click', '.service_reset', function () {
    resetForm();
})

$(document).on('submit', '.service_form', async function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = $(this).attr('make') === 'new' ? SERVICE_URL + '/new' : form.attr('action');
    const action = $(this).attr('make') === 'new' ? "création" : "modification";
    const bntSubmit = $(this).attr('make') === 'new' ? "Créer" : "Modifier";
    $(".service_submit").val('').addClass('disabled  btn-progress');
    await axios.post(url, formData).then(({ data }) => {
        if (data.action == "success") {
            toastr["success"]("success " + action);
            resetForm();
            $('#tableService').DataTable().ajax.reload();
        } else {
            toastr["error"]("echec de la " + action);
            appendError(data.errors);
            $(".service_submit").val('').removeClass('disabled btn-progress').val(bntSubmit);
        }
    });
});

$(document).on('submit', 'form.service_form_delete', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = form.attr('action');
    swal({
        title: 'Êtes-vous sûr?',
        text: 'Voulez vous supprimer ce service !!',
        buttons: true,
        dangerMode: true,
        closeOnConfirm: false,
    })
        .then(async (willDelete) => {
            if (willDelete) {
                form.children(".btnSubmitDeleteService").val('').addClass('disabled  btn-progress');
                await axios.delete(url, formData).then(({ data }) => {
                    if (data.action == "success") {
                        toastr["success"]("success suppression");
                        resetForm();
                        form.children(".btnSubmitDeleteService").val('').removeClass('disabled btn-progress').val('<i class="fas fa-trash"></i>');
                        $('#tableService').DataTable().ajax.reload();
                    } else {
                        toastr["error"]("echec de la suppression");
                    }
                });
            }
        });
});


$('#tableService').DataTable({
    dom: 'Bfrtip',
    "retrieve": true,
    "ordering": false,
    "pageLength": 5,
    ajax: SERVICE_URL + '/findAll',
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
            title: "edit",
            data: function (data) {
                const route = SERVICE_URL + '/' + data.id + '/edit';
                return ' <a class="btn btn-info btn-action mr-1 editeService" href="' + route + '" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fas fa-pencil-alt"></i></a>'
            }
        },
        {
            title: "delete",
            data: function (data) {
                const route = SERVICE_URL + '/' + data.id + '/delete';
                return ` <form action="${route}" class="service_form_delete" method="POST">
                            <button type="submit" class="btn btn-danger btn-action btnSubmitDeleteService" data-toggle="tooltip" data-original-title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>`;
            }
        },

    ],
    responsive: true,
});


function resetForm() {
    $(".service_submit").val('').removeClass('disabled btn-progress').val('Créer');
    $('.service_form :input').removeClass(' is-invalid');
    $('.service_form :input').next("ul").remove();
    $('.service_form').attr('action', '');
    $('.service_form').attr('make', 'new');
    $("#name").val('');
    $(".datepicker").val('');
    $('.service_submit').text('Créer');
}

$('#tableService_wrapper>.dt-buttons').css('display', 'none');


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