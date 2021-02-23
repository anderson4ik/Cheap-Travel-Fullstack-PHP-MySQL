
//adding "active" class for navbar by href
$(document).ready(function(){
    var fullpath = window.location.pathname;
    var filename = fullpath.replace(/^.*[\\\/]/, '');
    //alert(filename);
    if(!filename){
        filename = 'index.php';
    }
    var currentLink = $('a[href="' + filename + '"]'); //Selects the proper a tag
    currentLink.parent().addClass("active");
});


//animation to hide small message
$(".sm-box").delay(3000).slideUp();

//set summernote editor
$('#summernote').summernote({
        tabsize: 2,
        height: 120
});

//getting alert before deleting post
$(".delete_btn").on('click', function () {
    return confirm("Are you sure want to delete this post?");
}); 



