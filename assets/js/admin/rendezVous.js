
import axios from 'axios';
import { RV_URL } from '../config/config.js';
import swal from 'sweetalert';
import flatpickr from "flatpickr";
import toastr from "toastr";
import appendError from '../components/function.js';
require('flatpickr/dist/flatpickr.css');
require('toastr/build/toastr.css');

const fp_date_rv_rendezVous = flatpickr('.datepicker_date_rv_rendezVous', {
    enabledTime: true,
    altInput: true,
    altFormat: "j F Y, H:i",
    dateFormat: "Y-m-d H:i:S",
    defaultDate: "today"
});


$(document).on('click', '.rendezVous_new', async function (e) {
    e.preventDefault();
    resetForm();
    $('.action').text('Créer rendezVous');
    const url = $(this).attr('href');
    await axios.get(url).then(({ data }) => {
        $("#matricule").val(data);
    });
})

$(document).on('click', '.editeRendezVous', async function (e) {
    e.preventDefault();
    resetForm();
    $('.rendezVous_form').attr('make', 'edit');
    const url = $(this).attr('href');
    $(this).val('').addClass('disabled  btn-progress');
    await axios.get(url).then(({ data }) => {

        $(this).val('').removeClass('disabled btn-progress').val('<i class="fas fa-pencil-alt"></i>');
        $('.action').text('Edite rendezVous');
        $('.rendezVous_submit').text('Modifier');
        $('.rendezVous_form').attr('action', url);

        initSelect();
        $("#medecin_id option[value=" + data.medecin_id + "]").attr('selected', true);
        $("#secretary_id option[value=" + data.secretary_id + "]").attr('selected', true);
        $("#heure_rv option[value=" + data.heure_rv + "]").attr('selected', true);
        fp_date_rv_rendezVous._input.value = moment(data.date_rv).format("DD MMMM  Y, h:mm");
    });
});

function initSelect() {
    $("#medecin_id option").attr("selected", false);
    $("#secretary_id option").attr("selected", false);//init select
    $("#heure_rv option").attr("selected", false);//init select
}

$(document).on('click', '.rendezVous_reset', function () {
    $(".collapse").removeClass('show');
    resetForm();
})

$(document).on('submit', '.rendezVous_form', async function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = $(this).attr('make') === 'new' ? RV_URL + '/new' : form.attr('action');
    const action = $(this).attr('make') === 'new' ? "création" : "modification";
    const bntSubmit = $(this).attr('make') === 'new' ? "Créer" : "Modifier";
    $(".rendezVous_submit").val('').addClass('disabled  btn-progress');
    await axios.post(url, formData).then(({ data }) => {
        if (data.action == "success") {
            toastr["success"]("success " + action);
            resetForm();
            $(".collapse").removeClass('show');
            $('#tableRendezVous').DataTable().ajax.reload();
        } else {
            $(".rendezVous_submit").val('').removeClass('disabled btn-progress').val(bntSubmit);
            toastr["error"]("echec de la " + action);
            appendError(data.errors);
        }
    });
});

$(document).on('submit', 'form.rendezVous_form_delete', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const url = form.attr('action');
    swal({
        title: 'Êtes-vous sûr?',
        text: 'Voulez vous supprimer ce rendezVous !!',
        buttons: true,
        dangerMode: true,
        closeOnConfirm: false,
    })
        .then(async (willDelete) => {
            if (willDelete) {
                form.children(".btnSubmitDeleteRendezVous").val('').addClass('disabled  btn-progress');
                await axios.delete(url, formData).then(({ data }) => {
                    if (data.action == "success") {
                        toastr["success"]("success suppression");
                        resetForm();
                        form.children(".btnSubmitDeleteRendezVous").val('').removeClass('disabled btn-progress').val('<i class="fas fa-trash"></i>');
                        $('#tableRendezVous').DataTable().ajax.reload();
                    } else {
                        toastr["error"]("echec de la suppression");
                    }
                });
            }
        });
});


const table = $('#tableRendezVous').DataTable({
    dom: 'Bfrtip',
    "retrieve": true,
    "ordering": false,
    "pageLength": 5,
    ajax: RV_URL + '/findAll',
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
            title: "patient",
            data: function (data) {
                return ` <p class="font-weight-bold mb-0">${data.patient.genre == "HOMME" ? "Mr" : "Mme"} ${data.patient.firstName} ${data.patient.lastName}</p> `;
            }
        },
        {
            title: "medecin",
            data: function (data) {
                return ` <p class="font-weight-bold mb-0">${data.medecin.genre == "HOMME" ? "Mr" : "Mme"} ${data.medecin.firstName} ${data.medecin.lastName}</p> `;
            }
        },
        {
            title: "secretaire",
            data: function (data) {
                return ` <p class="font-weight-bold mb-0">${data.secretary.genre == "HOMME" ? "Mr" : "Mme"} ${data.secretary.firstName} ${data.secretary.lastName}</p> `;
            }
        },
        {
            title: "Heure",
            data: "heure_rv"
        },
        {
            title: "Date Rendez vous",
            data: function (data) {
                return moment(data.date_rv).format("DD MMMM YYYY");
            }
        },
        {
            title: "action",
            data: function (data) {
                const route_edite = RV_URL + '/' + data.rv_id + '/edit';
                const route_delete = RV_URL + '/' + data.rv_id + '/delete';
                return ` <div style="display: flex;"><a class="btn btn-info btn-action mr-1 editeRendezVous" href="${route_edite}" data-toggle="tooltip" title="" data-original-title="Edit"><i class="fas fa-pencil-alt"></i></a>
                    <form action="${route_delete}" class="rendezVous_form_delete" method="POST"  >
                                    <button type="submit" class="btn btn-danger btn-action btnSubmitDeleteRendezVous" data-toggle="tooltip" data-original-title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form> </div>`;
            }
        },
    ],
});



function resetForm() {
    $(".rendezVous_submit").val('').removeClass('disabled btn-progress').val('Créer');
    $('.rendezVous_form :input').removeClass(' is-invalid');
    $('.rendezVous_form :input').next("ul").remove();
    $('.rendezVous_form').attr('action', '');
    $('.rendezVous_form').attr('make', 'new');
    $("#name").val('');
    $(".datepicker").val('');
    $('.rendezVous_submit').text('Créer');
    $("#service_id option").attr("selected", false);//init select
}

$('#tableRendezVous_wrapper>.dt-buttons').css('display', 'none');


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

$("#myEvent").fullCalendar({
    height: 'auto',
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
    },
    editable: true,
    events: [
        {
            title: 'Conference',
            start: '2018-01-9',
            end: '2018-01-11',
            backgroundColor: "#fff",
            borderColor: "#fff",
            textColor: '#000'
        },
        {
            title: "John's Birthday",
            start: '2018-01-14',
            backgroundColor: "#007bff",
            borderColor: "#007bff",
            textColor: '#fff'
        },
        {
            title: 'Reporting',
            start: '2018-01-10T11:30:00',
            backgroundColor: "#f56954",
            borderColor: "#f56954",
            textColor: '#fff'
        },
        {
            title: 'Starting New Project',
            start: '2018-01-11',
            backgroundColor: "#ffc107",
            borderColor: "#ffc107",
            textColor: '#fff'
        },
        {
            title: 'Social Distortion Concert',
            start: '2018-01-24',
            end: '2018-01-27',
            backgroundColor: "#000",
            borderColor: "#000",
            textColor: '#fff'
        },
        {
            title: 'Lunch',
            start: '2018-01-24T13:15:00',
            backgroundColor: "#fff",
            borderColor: "#fff",
            textColor: '#000',
        },
        {
            title: 'Company Trip',
            start: '2018-01-28',
            end: '2018-01-31',
            backgroundColor: "#fff",
            borderColor: "#fff",
            textColor: '#000',
        },
    ]

});