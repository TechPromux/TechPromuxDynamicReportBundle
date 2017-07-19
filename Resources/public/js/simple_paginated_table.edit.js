/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {

    alert('hola');

    //$(document).on('click', '#{{form.vars.id}}_settings_property_target .btn.btn-link.sonata-collection-add', function () {
    $(document).on('sonata-collection-item-added', '#{{form.vars.id}}_settings_details_to_show', function () {

        var labels = $('#{{form.vars.id}}_settings_details_to_show label[data-ctype-modify="parent"]');
        for (var i = 0; i < labels.length; i++)
        {
            $(labels[i]).parent().addClass($(labels[i]).attr('data-ctype-modify-parent-addclass'));
        }
    });

    $(document).on('sonata-collection-item-added', '#{{form.vars.id}}_settings_details_to_order', function () {

        var labels = $('#{{form.vars.id}}_settings_details_to_order label[data-ctype-modify="parent"]');
        for (var i = 0; i < labels.length; i++)
        {
            $(labels[i]).parent().addClass($(labels[i]).attr('data-ctype-modify-parent-addclass'));
        }
    });

    $(document).on('sonata-collection-item-added', '#{{form.vars.id}}_settings_show_options', function () {

        var labels = $('#{{form.vars.id}}_settings_show_options label[data-ctype-modify="parent"]');
        for (var i = 0; i < labels.length; i++)
        {
            $(labels[i]).parent().addClass($(labels[i]).attr('data-ctype-modify-parent-addclass'));
        }

        var ul = $('#{{form.vars.id}}_settings_show_options ul[data-ctype-modify="child"]');
        for (var i = 0; i < $(ul).children().length; i++)
        {
            $($(ul).children()[i]).addClass($(ul).attr('data-ctype-modify-child-addclass'));
        }

    });

    $(document).on('sonata-collection-item-added', '#{{form.vars.id}}_settings_paginator_options', function () {

        var labels = $('#{{form.vars.id}}_settings_paginator_options label[data-ctype-modify="parent"]');
        for (var i = 0; i < labels.length; i++)
        {
            $(labels[i]).parent().addClass($(labels[i]).attr('data-ctype-modify-parent-addclass'));
        }
    });

    // Prepare Page 

    $('#{{form.vars.id}}_settings_show_options').addClass('container-fluid').trigger('sonata-collection-item-added');
    $('#{{form.vars.id}}_settings_paginator_options').addClass('container-fluid').trigger('sonata-collection-item-added');
    $('#{{form.vars.id}}_settings_details_to_show').addClass('container-fluid').trigger('sonata-collection-item-added');
    $('#{{form.vars.id}}_settings_details_to_order').addClass('container-fluid').trigger('sonata-collection-item-added');


    $('#sonata-ba-field-container-{{form.vars.id}}_name').parent().addClass('row');
    $('#sonata-ba-field-container-{{form.vars.id}}_name').addClass('col-md-12');
    $('#sonata-ba-field-container-{{form.vars.id}}_parent').addClass('col-md-6');
    $('#sonata-ba-field-container-{{form.vars.id}}_enabled').addClass('col-md-2');
    $('#sonata-ba-field-container-{{form.vars.id}}_position').addClass('col-md-2');


    $(document.createElement('hr')).insertAfter($($('#sonata-ba-field-container-{{form.vars.id}}_settings_query_id').children()[0]));
    $('#sonata-ba-field-container-{{form.vars.id}}_settings_query_id').append('<hr>');
    $(document.createElement('hr')).insertAfter($($('#sonata-ba-field-container-{{form.vars.id}}_settings_show_options').children()[0]));
    $('#sonata-ba-field-container-{{form.vars.id}}_settings_show_options').append('<hr>');
    $(document.createElement('hr')).insertAfter($($('#sonata-ba-field-container-{{form.vars.id}}_settings_paginator_options').children()[0]));
    $('#sonata-ba-field-container-{{form.vars.id}}_settings_paginator_options').append('<hr>');
    $(document.createElement('hr')).insertAfter($($('#sonata-ba-field-container-{{form.vars.id}}_settings_details_to_show').children()[0]));
    $('#sonata-ba-field-container-{{form.vars.id}}_settings_details_to_show').append('<hr>');
    $(document.createElement('hr')).insertAfter($($('#sonata-ba-field-container-{{form.vars.id}}_settings_details_to_order').children()[0]));
    $('#sonata-ba-field-container-{{form.vars.id}}_settings_details_to_order').append('<hr>');
});
