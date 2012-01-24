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
                $('head').append(header[i]);
            }
            
            var scripts = data.script;
            for(var i = 0; i < scripts.length; i++)
            {
                eval(scripts[i]);
            }
            sucessHandle();
        },
        error : function() 
        {
            failHandle();
        }
    });
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
                $('head').append(header[i]);
            }
            
            var scripts = data.script;
            for(var i = 0; i < scripts.length; i++)
            {
                eval(scripts[i]);
            }
            sucessHandle();
        },
        error : function() 
        {
            failHandle();
        }
    });
}