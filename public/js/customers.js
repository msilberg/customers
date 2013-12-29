/**
 * Created by mike on 12/26/13.
 */

var cFields = {};
cFields.customers = [
    {
        'name': 'id',
        'value': null,
        'required': false
    },{
        'name': 'fname',
        'value': null,
        'required': true
    },{
        'name': 'lname',
        'value': null,
        'required': true
    },{
        'name': 'phone',
        'value': null,
        'required': true
    },{
        'name': 'addr',
        'value': null,
        'required': false
    },{
        'name': 'status',
        'value': null,
        'required': false
    }
];
cFields.calls = [
    {
        'name': 'id',
        'value': null,
        'required': false
    },{
        'name': 'cname-select',
        'value': null,
        'required': true
    },{
        'name': 'call-subj',
        'value': null,
        'required': true
    },{
        'name': 'call-cont',
        'value': null,
        'required': true
    }
];
var bVars = {};

function end(array){
    var last_elm, key;
    if (array.constructor === Array){
        last_elm = array[(array.length-1)];
    }else{
        for (key in array){
            last_elm = array[key];
        }
    }
    return last_elm;
}
function validateForm(fields){
    var isValidated = true;
    $.each(fields, function(i,val){
        var fVal = ($('.' + val.name).is('select'))? $('.' + val.name).find(":selected").val() : $('.' + val.name).val();
        if (val.required && (fVal == "empty" || fVal.length == 0)){
            alert('Please specify field ' + $('.' + val.name + '-lbl').text());
            isValidated = false;
            return false;
        }else if (val.value == null){ val.value = fVal }
    });
    return isValidated;
}
function clearForm(fields){
    $.each(fields, function(i,val){
        if ($('.' + val.name).is('select')){
            $('.' + val.name).find('option').each(function(){
                if ($(this).val() == 'empty') $(this).attr('selected',true);
            }).each(function(){ $(this).removeAttr('selected') });
        }else{ $('.' + val.name).val('') }
        val.value = null;
    });
}
function addNewCustomer(){
    if (validateForm(cFields.customers)){
        $('body').css('opacity','0.4');
        $.post('/customers/add',
            { 'addcustomer': cFields.customers },
            function(){
                alert('A new customer ' + cFields.customers[1].value + ' ' + cFields.customers[2].value + ' has been successfully added');
                clearForm(cFields.customers);
                $('body').css('opacity','1.0');
            }
        );
    }
}
function addNewCall(){
    if (validateForm(cFields.calls)){
        $('body').css('opacity','0.4');
        $.post("/customers/add",
            { "addcalls": cFields.calls },
            function(){
                alert('A new call record ' + cFields.calls[2].value + ' was successfully added');
                clearForm(cFields.calls);
                $('body').css('opacity','1.0');
            }
        );
    }
}
function back2customersList(){
    $('.edit-form').hide();
    $('.edit-list').show();
}
function saveEditedCustomer(){
    if (validateForm(cFields.customers)){
        $.post('/customers/edit',
            {'savecustomer' : cFields.customers },
            function(){
                alert('Customer ' + cFields.customers[1].value + ' ' + cFields.customers[2].value + ' has been successfully updated');
                window.location = '/customers/edit?customer';
            }
        );
    }
}
function editCustomer(){
    $.post('/customers/edit',
        {'editcustomer' : $(this).attr('id') },
        function(data){
            var customer = data.customer;
            $('.edit-list').hide();
            $('.edit-form').show();
            $('.back2-edit-customers-list').on('click', back2customersList);
            $.each(cFields.customers, function(i,val){ $('.' + val.name).val(customer[val.name]) });
            cFields.customers[0].value = customer.id;
            $('.save-edit-customer').on('click', saveEditedCustomer);
        }
    );
}
function saveEditedCall(){
    if (validateForm(cFields.calls)){
        $.post('/customers/edit',
            {'savecall' : cFields.calls },
            function(){
                alert('Call ' + cFields.calls[2].value + ' has been successfully updated');
                window.location = '/customers/edit?call';
            }
        );
    }
}
function back2callsList(){
    $('.cname-edit-select').prop('disabled', false);
    $('.calls-edit-table').show();
    $('.back2-clist').show();
    $('.cname-select-prelbl').show();
    $('.edit-form').hide();
}
function editCall(){
    $('.cname-edit-select').prop('disabled', true);
    $('.calls-edit-table').hide();
    $('.back2-clist').hide();
    $('.cname-select-prelbl').hide();
    $.post('/customers/edit',
        { 'editcall': $(this).attr('id') },
        function(data){
            $.each(cFields.calls, function(i,val){
                if (val.name == 'cname-select') return true;
                $('.' + val.name).val(data[val.name]);
            });
            cFields.calls[0].value = data.id;
            $('.edit-form').show();
            $('.back2-edit-calls-list').on('click', back2callsList);
            $('.save-edit-call').on('click', saveEditedCall);
        }
    );

}
function getCalls(){
    var cId = $(this).val();
    if (cId == 'empty') return;
    $.post('/customers/edit',
        {'getcalls' : cId },
        function(data){
            if (typeof(data[0]) == 'string' && data[0] == 'empty'){
                alert('The customer you\'ve chosen doesn\'t have any calls.');
                $('.cname-edit-select').find('option').each(function(){ $(this).removeAttr('selected') });
            }else{
                var ceTbody = $('.calls-edit-table').find('tbody');
                ceTbody.empty();
                $.each(data, function(i,val){
                    ceTbody.append(
                        '<tr>' +
                            '<td>' + val['call-subj'] + '</td>' +
                            '<td>' +
                            '<a class="edit-call" id="' + val.id + '">Edit</a>' +
                            '</td>' +
                        '</tr>'
                    );
                });
                $('.edit-call').on('click', editCall);
                $('.calls-edit-table').show();
            }
        }
    );
}
function trackUrl(){
    var ind;
    var path = end(window.location.pathname.split('/'));
    var param = end(window.location.href.split('?'));
    switch (path){
        case 'add':
            switch (param){
                case 'customer': ind = 1;
                    break;
                case 'call': ind = 2;
                    break;
            }
            break;
        case 'edit':
            switch (param){
                case 'customer': ind = 3;
                    break;
                case 'call': ind = 4;
                    break;
            }
            break;
        case 'rss':
            ind = 5;
            loadRSS('http://feeds.feedburner.com/webupd8?format=xml', outputRSS);
            break;
        default: ind = 0;
    }
    $('ul.navbar-nav').find('li').eq(ind).addClass('active');
}
function assignHandlers(){
    $('.add-customer').on('click', addNewCustomer);
    $('.add-call').on('click', addNewCall);
    $('.edit-customer').on('click', editCustomer);
    $('.cname-edit-select').on('change', getCalls);
}
/**
 * RSS Block
 */
function printFeed(){
    $('#news-cont').printThis({
        debug: false,
        importCSS: true,
        printContainer: true,
        loadCSS: "",
        pageTitle: "",
        removeInline: false,
        printDelay: 333,
        header: null
    });
}
function closeFeed(){
    $('html,body').removeAttr('style');
    $('.grey-bckgr').remove();
    $('.rss-news-block').remove();
}
function showFeed(){
    if (confirm('Do you really want to open this feed?')){
        var ind = $('.article').index(this);
        $('html,body').animate({scrollTop: 0}, 0).
            css("overflow", "hidden").
            append(
                '<div class="grey-bckgr"></div>' +
                '<div class="rss-news-block">' +
                    '<div class="news-block-cnt">' +
                        '<ul>' +
                            '<li><div class="print-article">Print</div></li>' +
                            '<li><div class="close-article">Close</div></li>' +
                        '</ul>' +
                    '</div><div id="news-cont">' +
                    bVars.rssFeeds[ind].content +
                    '</div>' +
                '</div>'
            );
        $('.close-article').on('click', closeFeed);
        $('.print-article').on('click', printFeed);
    }
}
function outputRSS(feeds){
    var rssCont = $('.my-rss');
    bVars.rssFeeds = feeds.entries;
    $('#fountainG').remove();
    rssCont.append('<h2 class="rss-header">' + feeds.description + '</h2><br /><br />');
    $.each(feeds.entries, function(i,val){
        rssCont.append(
            '<div class="article">' +
                '<h4>' + val.title + '</h4>' +
                '<i>' + val.author + '</i>' +
                '<p>' + val.contentSnippet + '</p>' +
            '</div><hr />'
        );
    });
    $('.article').on('click', showFeed);
}
function loadRSS(url, callback) {
    $.ajax({
        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent(url),
        dataType: 'json',
        success: function(data) {
            callback(data.responseData.feed);
        }
    });
}


$(document).ready(function(){
    assignHandlers();
    trackUrl();
});