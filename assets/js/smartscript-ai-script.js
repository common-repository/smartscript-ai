jQuery(document).ready(function($){
    $('#ssai_generate').on('click', function(){
        var prompt = $('#ssai_prompt').val();
        var language = $('#ssai_language').val();

        if(prompt === '') {
            alert('Please enter a prompt.');
            return;
        }

        $('#ssai_generate').attr('disabled', true).text('Generating...');
        $('#ssai_result').html(''); 
        // Clear previous results or errors

        $.ajax({
            url: ssai_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'ssai_generate_content',
                prompt: prompt,
                language: language, 
                ssai_nonce: ssai_ajax_object.ssai_nonce // Include nonce for security
            },
            success: function(response) {
                $('#ssai_generate').attr('disabled', false).text('Generate Content');
                if(response.success) {
                    // Insert the generated content into the post editor
                    insertContentIntoEditor(response.data);
                    // Optionally display a success message
                    $('#ssai_result').html('<p style="color:green;"><strong>Content inserted into the editor.</strong></p>');
                } else {
                    $('#ssai_result').html('<p style="color:red;"><strong>Error:</strong> ' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#ssai_generate').attr('disabled', false).text('Generate Content');
                $('#ssai_result').html('<p style="color:red;"><strong>Error:</strong> An unexpected error occurred.</p>');
            }
        });
    });

    function insertContentIntoEditor(content) {
        // Check if Gutenberg editor is active
        if (typeof wp.data !== 'undefined') {
            // For Gutenberg Block Editor
            wp.data.dispatch('core/block-editor').insertBlocks(
                wp.blocks.createBlock('core/paragraph', {
                    content: content
                })
            );
        } else if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
            // For Classic Editor with Visual tab active
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, content);
        } else {
            // For Classic Editor with Text tab active
            var textarea = $('#content');
            textarea.val(textarea.val() + content);
        }
    }
});