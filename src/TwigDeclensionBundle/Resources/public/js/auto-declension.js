autoDeclension = function() {
    if(!$('input[twig-declension-form]').length){
        return;
    }
    $('input[twig-declension-form="inf"]').blur(function(){
        var url = Routing.generate('admin_twig_declension_guess');
        $.post(
            url,
            {
                infinitive: $(this).val()
            },
            function (data){
                if(data['status'] === 'success'){
                    for(var form in data['declensions']){
                        $('input[twig-declension-form="' + form + '"]').val(data['declensions'][form]);
                    }
                }
            }
        );
    });
};

$(document).ready(function(){
    autoDeclension();
});