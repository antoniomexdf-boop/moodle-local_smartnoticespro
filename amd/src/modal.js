define([], function() {
    'use strict';

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
            var sesskey = modal.getAttribute('data-sesskey');
            var courseid = modal.getAttribute('data-courseid') || '0';
            var pageurl = window.location.pathname || '';

            if (!noticeid || !sesskey) {
                return;
            }

            state[eventname] = true;
            var body = new URLSearchParams();
            body.append('noticeid', noticeid);
            body.append('event', eventname);
            body.append('sesskey', sesskey);
            body.append('courseid', courseid);
            body.append('pageurl', pageurl);

            fetch(M.cfg.wwwroot + '/local/smartnoticespro/track.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: body.toString()
            }).catch(function() {
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
