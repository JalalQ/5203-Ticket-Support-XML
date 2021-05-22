
//the purpose of this javascript is to color ticket with "open" status as green,
//and those with "close" status as red.
window.onload = function () {
    var status = document.getElementsByClassName("status");

    for (var i=0; i<status.length; i++) {
        if (status[i].lastChild.data == "Open") {
            status[i].style.color= "green";
        }
        else {
            status[i].style.color= "red";
        }
    }

}