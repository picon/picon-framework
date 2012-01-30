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

function processResults(response)
{
    try
    {
        var data = jQuery.parseJSON(response);
    }
    catch(err)
    {
        return false;
    }
    
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