function sortTable(n, tableid) {
    var table = 0;
    var rows = 0;
    var switching = 0;
    var i = 0;
    var x = 0;
    var y = 0;
    var shouldSwitch = 0;
    var dir = 0;
    var switchcount = 0;
    tablecolor=true;

    table = document.getElementById(tableid);
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
    no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /* Loop through all table rows (except the
        first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
            one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];

            /* Check if the two rows should switch place,
            based on the direction, asc or desc: */
            if (dir == "asc") {
                if (parseInt(x.innerHTML.toLowerCase()) > parseInt(y.innerHTML.toLowerCase())) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    if(tablecolor==true){

                    }
                    break;
                }
            } else if (dir == "desc") {
                if (parseInt(x.innerHTML.toLowerCase()) < parseInt(y.innerHTML.toLowerCase())) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
            and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount ++;
        } else {
            /* If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }

    //this iteration change the color of every two row to prevent them to mix
    for(i=1;i<rows.length;i++){

        if(tablecolor==true){
            rows[i].style.setProperty('background-color', 'white');
            tablecolor=false;
        }else{
            rows[i].style.setProperty('background-color', '#DDDDDD');
            tablecolor=true;
        }

    }
}