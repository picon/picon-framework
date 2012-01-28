function piconAjaxGet(getUrl, sucessHandle, failHandle)
{
    $.ajax({
        url: getUrl,
        dataType : "json",
        context: document.body,
        success: function(data)
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
                        $('head').append(this);
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
            sucessHandle();
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
        dataType : "json",
        data : $('#'+formId).serialize(),
        success: function(data)
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
                    if(!scriptExists(this.src))
                    {
                        $('head').append(this);
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
            sucessHandle();
        },
        error : function() 
        {
            failHandle();
        }
    });
}