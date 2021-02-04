/**
 * Data source
 * @type {jQuery}
 */
var pictures = $( "form" ).data( "pictures" );

/**
 * List pictures
 * @param result
 */
function urls(result) {

    if(!pictures) pictures = {};
    var newPictures = {};
    var files = result.split("\n");

    files.forEach(function(item, i, arr) {
       if(!pictures[item])newPictures[item] = item;
    });


   if(! $.isEmptyObject(newPictures))
       request(newPictures);

}


/**
 * Show pictures
 * @param newPictures
 */
function showPicture(newPictures) {
    newPictures = JSON.parse(newPictures);
    for(var prop in newPictures){
        pictures[newPictures[prop]]=newPictures[prop];
        $("#content").append("<img class=\"flex-item\" src='"+newPictures[prop]+"'/>");
    }
    $( "form" ).data( "pictures",pictures );
}


/**
 * Generator items
 * @param arr
 */
function* gen(arr) {

    var newArr=[];
    var arr = Object.keys(arr);

    var numb = 4;

    for(let i=0; i<arr.length; i++){
        newArr.push(arr[i]);

        if(i>=numb){
            yield newArr;
            newArr=[];
            numb+=4;
        } else if(i==arr.length-1){
            yield newArr;
            newArr=[];
        }
    }
}

/**
 * Load pictures
 * @param newPictures
 */
function request(newPictures){

    var generator = gen(newPictures);
    let obj;
    while( obj = generator.next()  ){
        if(obj.value != undefined){

            var formData = new FormData();
            formData.append('files',JSON.stringify(obj.value ));

            $.ajax({
                type: "POST",
                url: '/request',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data, textStatus, jqXHR) {
                    showPicture(data);
                },
                error: function (data, textStatus, jqXHR) {
                    console.log('error',data);
                }
            });

        }else{
            break;
        }
    }
}


/**
 * Submit event
 */
$("form").on("submit",function(e){

     e.preventDefault();

    new Promise(function(resolve, reject) {
            var reader = new FileReader();
            var file = this.elements.file.files[0];
            reader.onload=function(){
                resolve(this.result);
            };
            reader.readAsBinaryString(file);

          }.bind(this)).then( result => {
              urls(result);
          });

     return false;
});



