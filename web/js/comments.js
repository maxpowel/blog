(function(window, $, easyXDM) {
    "use strict";
    var FOS_COMMENT = {
        post: function(url, data, success, error, complete) {
            var wrappedErrorCallback = function(response) {
                if ('undefined' !== typeof error) {
                    error(response.responseText, response.status);
                }
            };
            var wrappedCompleteCallback = function(response) {
                if ('undefined' !== typeof complete) {
                    complete(response.responseText, response.status);
                }
            };
            $.post(url, data, success).error(wrappedErrorCallback).complete(wrappedCompleteCallback);
        },

        get: function(url, data, success, error) {
            var wrappedErrorCallback = function(response) {
                if ('undefined' !== typeof error) {
                    error(response.responseText, response.status);
                }
            };
            $.get(url, data, success).error(wrappedErrorCallback);
        },

        getThreadComments: function(identifier, permalink) {
            var event = jQuery.Event('fos_comment_before_load_thread');

            event.identifier = identifier;
            event.params = {
                permalink: encodeURIComponent(permalink || window.location.href)
            };

            FOS_COMMENT.thread_container.trigger(event);
            FOS_COMMENT.get(
                FOS_COMMENT.base_url + '/' + encodeURIComponent(event.identifier) + '/comments',
                event.params,
                function(data) {
                    FOS_COMMENT.thread_container.html(data);
                    FOS_COMMENT.thread_container.attr('data-thread', event.identifier);
                    FOS_COMMENT.thread_container.trigger('fos_comment_load_thread', event.identifier);
                }
            );
        },

        initializeListeners: function() {
            FOS_COMMENT.thread_container.on('submit',
                'form.fos_comment_comment_new_form',
                function(e) {
                    var that = $(this);
                    var serializedData = FOS_COMMENT.serializeObject(this);

                    e.preventDefault();

                    var event = $.Event('fos_comment_submitting_form');
                    that.trigger(event);

                    if (event.isDefaultPrevented()) {
                        return;
                    }

                    FOS_COMMENT.post(
                        this.action,
                        serializedData,
                        function(data, statusCode) {
                            FOS_COMMENT.appendComment(data, that);
                            that.trigger('fos_comment_new_comment', data);
                            if (that.data() && that.data().parent !== '') {
                                that.parents('.fos_comment_comment_form_holder').remove();
                            }
                        },
                        function(data, statusCode) {
                            var parent = that.parent();
                            parent.after(data);
                            parent.remove();
                        },
                        function(data, statusCode) {
                            that.trigger('fos_comment_submitted_form', statusCode);
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_reply_show_form',
                function(e) {
                    var form_data = $(this).data();
                    var that = $(this);

                    if (that.closest('.fos_comment_comment_reply').hasClass('fos_comment_replying')) {
                        return that;
                    }

                    FOS_COMMENT.get(
                        form_data.url, {
                            parentId: form_data.parentId
                        },
                        function(data) {
                            that.closest('.fos_comment_comment_reply').addClass('fos_comment_replying');
                            that.after(data);
                            that.trigger('fos_comment_show_form', data);
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_reply_cancel',
                function(e) {
                    var form_holder = $(this).closest('.fos_comment_comment_form_holder');

                    var event = $.Event('fos_comment_cancel_form');
                    form_holder.trigger(event);

                    if (event.isDefaultPrevented()) {
                        return;
                    }

                    form_holder.closest('.fos_comment_comment_reply').removeClass('fos_comment_replying');
                    form_holder.remove();
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_edit_show_form',
                function(e) {
                    var form_data = $(this).data();
                    var that = $(this);

                    FOS_COMMENT.get(
                        form_data.url, {},
                        function(data) {
                            var commentBody = $(form_data.container);

                            commentBody.data('original', commentBody.html());

                            commentBody.html(data);

                            that.trigger('fos_comment_show_edit_form', data);
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('submit',
                'form.fos_comment_comment_edit_form',
                function(e) {
                    var that = $(this);

                    FOS_COMMENT.post(
                        this.action,
                        FOS_COMMENT.serializeObject(this),
                        function(data) {
                            FOS_COMMENT.editComment(data);
                            that.trigger('fos_comment_edit_comment', data);
                        },

                        function(data, statusCode) {
                            var parent = that.parent();
                            parent.after(data);
                            parent.remove();
                        }
                    );

                    e.preventDefault();
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_edit_cancel',
                function(e) {
                    FOS_COMMENT.cancelEditComment($(this).parents('.fos_comment_comment_body'));
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_vote',
                function(e) {
                    var that = $(this);
                    var form_data = that.data();

                    FOS_COMMENT.get(
                        form_data.url, {},
                        function(data) {
                            var form = $($.trim(data)).children('form')[0];
                            var form_data = $(form).data();

                            FOS_COMMENT.post(
                                form.action,
                                FOS_COMMENT.serializeObject(form),
                                function(data) {
                                    $('#' + form_data.scoreHolder).html(data);
                                    that.trigger('fos_comment_vote_comment', data, form);
                                }
                            );
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_comment_remove',
                function(e) {
                    var form_data = $(this).data();

                    var event = $.Event('fos_comment_removing_comment');
                    $(this).trigger(event);

                    if (event.isDefaultPrevented()) {
                        return;
                    }

                    FOS_COMMENT.get(
                        form_data.url, {},
                        function(data) {
                            var form = $($.trim(data)).children('form')[0];

                            FOS_COMMENT.post(
                                form.action,
                                FOS_COMMENT.serializeObject(form),
                                function(data) {
                                    var commentHtml = $($.trim(data));

                                    var originalComment = $('#' + commentHtml.attr('id'));

                                    originalComment.replaceWith(commentHtml);
                                }
                            );
                        }
                    );
                }
            );

            FOS_COMMENT.thread_container.on('click',
                '.fos_comment_thread_commentable_action',
                function(e) {
                    var form_data = $(this).data();

                    FOS_COMMENT.get(
                        form_data.url, {},
                        function(data) {
                            var form = $($.trim(data)).children('form')[0];

                            FOS_COMMENT.post(
                                form.action,
                                FOS_COMMENT.serializeObject(form),
                                function(data) {
                                    var form = $($.trim(data)).children('form')[0];
                                    var threadId = $(form).data().fosCommentThreadId;

                                    FOS_COMMENT.getThreadComments(threadId);
                                }
                            );
                        }
                    );
                }
            );
        },

        appendComment: function(commentHtml, form) {
            var form_data = form.data();

            if ('' != form_data.parent) {
                var reply_button_holder = form.closest('.fos_comment_comment_reply');

                var comment_element = form.closest('.fos_comment_comment_show')
                    .children('.fos_comment_comment_replies');

                reply_button_holder.removeClass('fos_comment_replying');

                comment_element.prepend(commentHtml);
                comment_element.trigger('fos_comment_add_comment', commentHtml);
            } else {
                form.after(commentHtml);
                form.trigger('fos_comment_add_comment', commentHtml);

                form = $(form[0]);
                form[0].reset();
                form.children('.fos_comment_form_errors').remove();
            }
        },

        editComment: function(commentHtml) {
            var commentHtml = $($.trim(commentHtml));
            var originalCommentBody = $('#' + commentHtml.attr('id')).children('.fos_comment_comment_body');

            originalCommentBody.html(commentHtml.children('.fos_comment_comment_body').html());
        },

        cancelEditComment: function(commentBody) {
            commentBody.html(commentBody.data('original'));
        },

        serializeObject: function(obj) {
            var o = {};
            var a = $(obj).serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        },

        loadCommentCounts: function() {
            var threadIds = [];
            var commentCountElements = $('span.fos-comment-count');

            commentCountElements.each(function(i, elem) {
                var threadId = $(elem).data('fosCommentThreadId');
                if (threadId) {
                    threadIds.push(threadId);
                }
            });

            FOS_COMMENT.get(
                FOS_COMMENT.base_url + '.json', {
                    ids: threadIds
                },
                function(data) {
                    if (typeof data != "object") {
                        data = jQuery.parseJSON(data);
                    }

                    var threadData = {};

                    for (var i in data.threads) {
                        threadData[data.threads[i].id] = data.threads[i];
                    }

                    $.each(commentCountElements, function() {
                        var threadId = $(this).data('fosCommentThreadId');
                        if (threadId) {
                            FOS_COMMENT.setCommentCount(this, threadData[threadId]);
                        }
                    });
                }
            );

        },

        setCommentCount: function(elem, threadObject) {
            if (threadObject == undefined) {
                elem.innerHTML = '0';

                return;
            }

            elem.innerHTML = threadObject.num_comments;
        }
    };

    FOS_COMMENT.thread_container = window.fos_comment_thread_container || $('#fos_comment_thread');

    if (typeof window.fos_comment_remote_cors_url != "undefined") {
        FOS_COMMENT.easyXDM = easyXDM.noConflict('FOS_COMMENT');

        FOS_COMMENT.request = function(method, url, data, success, error) {
            var wrappedSuccessCallback = function(response) {
                if ('undefined' !== typeof success) {
                    success(response.data, response.status);
                }
            };
            var wrappedErrorCallback = function(response) {
                if ('undefined' !== typeof error) {
                    error(response.data.data, response.data.status);
                }
            };

            FOS_COMMENT.xhr.request({
                url: url,
                method: method,
                data: data
            }, wrappedSuccessCallback, wrappedErrorCallback);
        };

        FOS_COMMENT.post = function(url, data, success, error) {
            this.request('POST', url, data, success, error);
        };

        FOS_COMMENT.get = function(url, data, success, error) {
            var params = jQuery.param(data);
            url += '' != params ? '?' + params : '';

            this.request('GET', url, undefined, success, error);
        };

        FOS_COMMENT.xhr = new FOS_COMMENT.easyXDM.Rpc({
            remote: window.fos_comment_remote_cors_url
        }, {
            remote: {
                request: {}
            }
        });
    }

    FOS_COMMENT.base_url = window.fos_comment_thread_api_base_url;

    if (typeof window.fos_comment_thread_id != "undefined") {
        FOS_COMMENT.getThreadComments(window.fos_comment_thread_id);
    }

    if (typeof window.fos_comment_thread_comment_count_callback != "undefined") {
        FOS_COMMENT.setCommentCount = window.fos_comment_thread_comment_count_callback;
    }

    if ($('span.fos-comment-count').length > 0) {
        FOS_COMMENT.loadCommentCounts();
    }

    FOS_COMMENT.initializeListeners();

    window.fos = window.fos || {};
    window.fos.Comment = FOS_COMMENT;
})(window, window.jQuery, window.easyXDM);