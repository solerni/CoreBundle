/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function () {
    'use strict';

    var homePath = $('#homePath').html(); //global

    function save(id)
    {
        var ready = $.Deferred();
        var name = $('#theme-name').val();
        var themeLess = $('#theme-less textarea').val();
        var variables = '';

        $('.theme-value input').each(function () {
            variables +=  $('code', this.parentNode.parentNode).html() + ': ' + this.value + ';\n';
        });

        $.post(homePath + 'admin/theme/build', {
            'theme-id': id,
            'theme-less': themeLess,
            'name': name,
            'variables': variables
        })
        .done(function (data) {
            if (!isNaN(data) && data !== '') {

                if (name === '' || name === undefined) {
                    $('#theme-name').val('Theme' + data);
                }

                ready.resolve(data);

            } else {
                modal('admin/theme/error');
            }
        })
        .error(function () {
            modal('admin/theme/error');
        });

        return ready;
    }

    function modal(url, name, id)
    {
        $('.modal').modal('hide');

        name = typeof(name) !== 'undefined' ? name : null;
        id = typeof(id) !== 'undefined' ? id : null;

        $.ajax(homePath + url)
        .done(function (data) {
            var modal = document.createElement('div');
            modal.className = 'modal fade';

            if (name) {
                modal.setAttribute('id', name);
            }

            if (id) {
                $(modal).data('id', id);
            }

            modal.innerHTML = data;

            $(modal).appendTo('body');
            $(modal).modal('show');

            $(modal).on('hidden', function () {
                $(this).remove();
            });
        });
    }

    $('body').on('click', '.theme-generator .btn.dele', function () {

        modal('admin/theme/confirm', 'delete-theme', $(this).data('id'));
    });

    $('body').on('click', '.theme-generator .alert .close', function () {

        modal('admin/theme/confirm', 'delete-theme', $(this).data('id'));
    });

    $('body').on('click', '.#delete-theme .btn.delete', function () {
        var id = $('#delete-theme').data('id');

        $.ajax(homePath + 'admin/theme/delete/' + id)
        .done(function (data) {
            if (data === 'true') {
                window.location = homePath + 'admin/theme/list';
            } else {
                modal('admin/theme/error');
            }
        })
        .error(function () {
            modal('admin/theme/error');
        });
    });

    $('body').on('click', '.theme-generator .btn.save', function () {
        save($(this).data('id'))
        .done(function () {
            window.location = homePath + 'admin/theme/list';
        });
    });

    $('body').on('click', '.theme-generator .btn.preview', function () {
        save($(this).data('id'))
        .done(function (data) {
            window.location = homePath + 'admin/theme/preview/' + data;
        });
    });

    $('.theme-value .btn').each(function () {
        $(this).colorpicker().on('changeColor', function (event) {
            $('input', this.parentNode.parentNode).val(event.color.toHex());
        });
    });

    $('.theme-value .btn').on('click', function () {
        var color = $('input', this.parentNode.parentNode).val();

        if (/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color)) {
            $(this).colorpicker('setValue', color);
        }
    });

}());
