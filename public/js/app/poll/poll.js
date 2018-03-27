// add new choice input

$('#addPoll').on('click',function(e){
    e.preventDefault();
   var nextpollChoice = $('.responses > li').length;
   var template = '<li class="form-material"><input type="text" class="form-control" name="response['+nextpollChoice+']" >';
   $('.responses').append(template);
});