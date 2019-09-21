jQuery(document).ready(function ($) {
    type = $('#ppn-post-type');
    nonce = $('#ppn-nonce');
    tagId = $('#ppn-tag-id');
    categoryId = $('#ppn-category-id');

    type.select2({
        placeholder: 'Select Post Type'
    });


    type.on('change', function () {
      $('#ppn-tag-id').val(null).trigger('change');
      $('#ppn-category-id').val(null).trigger('change');
    });

  tagId.select2({
        placeholder: 'Choose Tag',
        allowClear: true,
        ajax: {
            url: _ajax.url,
            method: 'GET',
            data: function (params) {
                return {
                    'ppn-nonce': nonce.val(),
                    action: 'ppn-load-terms',
                    post_type: type.val(),
                    tax_type: 'tag',
                    q: params.term
                };
            },
            processResults: function (tags, params) {
                jQuery('.select2-search__field').trigger('focus');
                return {
                    results: $.map(tags, function (tag) {
                        return {
                            text: tag.name,
                            slug: tag.name,
                            id: tag.term_taxonomy_id
                        };
                    })
                };
            }
        }
    });

    categoryId.select2({
        placeholder: 'Choose Category',
        allowClear: true,
        ajax: {
            url: _ajax.url,
            method: 'GET',
            data: function (params) {
                return {
                    'ppn-nonce': nonce.val(),
                    action: 'ppn-load-terms',
                    post_type: type.val(),
                    tax_type: 'category',
                    q: params.term
                };
            },
            processResults: function (tags, params) {
                jQuery('.select2-search__field').trigger('focus');
                return {
                    results: $.map(tags, function (tag) {
                        return {
                            text: tag.name,
                            slug: tag.name,
                            id: tag.term_taxonomy_id
                        };
                    })
                };
            }
        }
    });

    $('#ppn-button-pdf-save').click(function (e) {
        e.preventDefault();
        form = new FormData($('#ppn-pdf-form')[0]);
        form.append('action', 'ppn-save-pdf');
        $.ajax({
            url: _ajax.url,
            data: form,
            processData: false,
            contentType: false,
            type: 'POST'
        }).done(function (json) {
            if (json.response) {
                mediaButton = $('#ppn-button-pdf-view');
                mediaButton.show();
                mediaButton.data('mediaUrl', json.link);
                $('.ppn-icon-success').show();
                $('#ppn-button-pdf-save').prop('disabled', true);
            }
        })
    });

    $('#ppn-button-pdf-view').click(function () {
        window.open($(this).data().mediaUrl);
    });

    var mediaUploader;

    $('#upload_image_button').click(function (e) {
      e.preventDefault();
      // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      // Extend the wp.media object
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
          text: 'Choose Image'
        },
        multiple: false,
        library: {
          type: 'image',
        }
      });

      // When a file is selected, grab the URL and set it as the text field's value
      mediaUploader.on('select', function () {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#image-url').text(attachment.name.substr(0, 19) + '...');
        $('#image-url').show();
        $('#image_attachment_id').val(attachment.id);
      });
      // Open the uploader dialog
      mediaUploader.open();
    });

});
