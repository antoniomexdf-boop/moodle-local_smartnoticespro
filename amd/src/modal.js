// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Modal controller for Smart Notices Pro.
 *
 * @module     local_smartnoticespro/modal
 * @copyright  2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax'], function(Ajax) {
    'use strict';

    /**
     * Initialise modal queue rendering.
     *
     * @param {string|string[]} modalids Modal element ids.
     */
    function init(modalids) {
        var ids = Array.isArray(modalids) ? modalids : [modalids];
        var modals = ids.map(function(id) {
            return document.getElementById(id);
        }).filter(function(modal) {
            return !!modal;
        });

        if (!modals.length) {
            return;
        }

        var previouslyFocused = document.activeElement;
        var currentindex = -1;

        var track = function(modal, eventname, state) {
            if (!modal || state[eventname]) {
                return;
            }

            var noticeid = modal.getAttribute('data-noticeid');
            var courseid = modal.getAttribute('data-courseid') || '0';
            var pageurl = window.location.pathname || '';

            if (!noticeid) {
                return;
            }

            state[eventname] = true;
            Ajax.call([{
                methodname: 'local_smartnoticespro_track_notice_event',
                args: {
                    noticeid: parseInt(noticeid, 10),
                    eventname: eventname,
                    courseid: parseInt(courseid, 10),
                    pageurl: pageurl
                }
            }])[0].catch(function() {
                // Silent fail for tracking.
            });
        };

        var showModal = function(index) {
            if (index < 0 || index >= modals.length) {
                document.body.classList.remove('smartnotices-modal-open');
                if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
                    previouslyFocused.focus();
                }
                return;
            }

            currentindex = index;
            var modal = modals[currentindex];
            document.body.classList.add('smartnotices-modal-open');
            modal.classList.add('is-visible');
            modal.setAttribute('aria-hidden', 'false');
            modal.focus();
        };

        var hideCurrentAndNext = function() {
            if (currentindex < 0 || currentindex >= modals.length) {
                return;
            }

            var modal = modals[currentindex];
            modal.classList.remove('is-visible');
            modal.setAttribute('aria-hidden', 'true');
            showModal(currentindex + 1);
        };

        modals.forEach(function(modal) {
            var state = {
                close: false,
                confirm: false
            };

            var closeButtons = modal.querySelectorAll('[data-action="close"]');
            var confirmButtons = modal.querySelectorAll('[data-action="confirm"]');

            var onClose = function() {
                track(modal, 'close', state);
                hideCurrentAndNext();
            };

            var onConfirm = function() {
                track(modal, 'confirm', state);
                hideCurrentAndNext();
            };

            closeButtons.forEach(function(button) {
                button.addEventListener('click', onClose);
            });

            confirmButtons.forEach(function(button) {
                button.addEventListener('click', onConfirm);
            });

            modal.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    onClose();
                }
            });
        });

        showModal(0);
    }

    return {
        init: init
    };
});
