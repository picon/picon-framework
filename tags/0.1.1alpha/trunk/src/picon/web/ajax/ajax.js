function piconAjaxGet(getUrl, sucessHandle, failHandle)
{
    $.ajax({
        url: getUrl,
        context: document.body,
        success: function(data)
        {
            if(processResults(data))
            {
                sucessHandle();
            }
            else
            {
                failHandle();
            }
        },
        error : function() 
        {
            failHandle();
        }
    });
}

function scriptExists(scriptSrc)
{
    var exists = false;
    $('head script').each(function()
    {
        if(this.src==scriptSrc)
        {
            exists = true;
        }
    });
    return exists;
}

function piconAjaxSubmit(formId, postUrl, sucessHandle, failHandle)
{
    var uploads = $('input[type="file"]', $('#'+formId));
    if(uploads.length>0)
    {
        piconIframeSubmit(formId, postUrl, sucessHandle, failHandle);
    }
    else
    {
        piconPost(formId, postUrl, sucessHandle, failHandle);
    }
}

function piconIframeSubmit(formId, postUrl, sucessHandle, failHandle)
{
    var id = formId + '_' + (new Date().getTime());
    var iframe = $('<iframe id="' + id + '" name="' + id + '" style="position:absolute; top:-9999px; left:-9999px"></iframe>');
    iframe.insertAfter($('#'+formId));
    
    var submitForm = $('#'+formId);
    var restoreAction = submitForm.attr('action');
    var restoreTarget = submitForm.attr('target');
    submitForm.attr('action', postUrl);
    submitForm.attr('target', id);
    submitForm.submit();
    
    submitForm.bind('submit', function() 
    {
        return false;
    });
    
    iframe.load(function()
    {
        var frameContents = $('#'+id).contents();
        var data = jQuery.parseJSON(frameContents.text());
        
        if(data)
        {
            if(processResults(data))
            {
                sucessHandle();
            }
            else
            {
                failHandle();
            }
        }
        else
        {
            failHandle();
        }
        alert(restoreTarget);
        submitForm.attr('action', restoreAction==undefined?'':restoreAction);
        submitForm.attr('target', restoreTarget==undefined?'':restoreTarget);
        submitForm.unbind('submit');
    });
}

function piconPost(formId, postUrl, sucessHandle, failHandle)
{
    $.ajax({
        url: postUrl,
        type: "POST",
        context: document.body,
        data : $('#'+formId).serialize(),
        success: function(data)
        {
            if(processResults(data))
            {
                sucessHandle();
            }
            else
            {
                failHandle();
            }
        },
        error : function() 
        {
            failHandle();
        }
    });
}

function processResults(data)
{    
    var components = data.components;

    for(var i = 0; i < components.length; i++)
    {
        $('#'+components[i].id).replaceWith(components[i].value);
    }

    var header = data.header;

    for(var i = 0; i < header.length; i++)
    {
        $(header[i]).filter('script').each(function()
        {
            var inner = this.innerText || this.textContent || '';

            if(inner=='')
            {
                if(!scriptExists(this.src))
                {
                    $('head').append(this);
                }
            }
            else
            {
                var scriptElement = $('<script type="text/javascript">'+inner+'</script>')
                $('head').append(scriptElement);
            }
        });
        $(header[i]).filter(':not(script)').each(function()
        {
            $('head').append(this);
        });
    }

    var scripts = data.script;
    for(var i = 0; i < scripts.length; i++)
    {
        jQuery.globalEval(scripts[i]);
    }
    

}