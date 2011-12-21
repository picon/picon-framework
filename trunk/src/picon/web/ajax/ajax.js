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