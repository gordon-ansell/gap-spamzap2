function expandlog(cnt) 
{
    var p = document.getElementById("eplus-" + cnt);
    var m = document.getElementById("eminus-" + cnt);
    var s = document.getElementById("subrow-" + cnt);

    if (p.style.display === "none") {
        p.style.display = "inline";
        m.style.display = "none";
        s.style.display = "none";
    } else {
        p.style.display = "none";
        m.style.display = "inline";
        s.style.display = "block";
    }
}

function expandlogrecord(cnt) 
{
    var p = document.getElementById("eplus-" + cnt);
    var m = document.getElementById("eminus-" + cnt);
    var s = document.getElementById("subrow-" + cnt + "-1");

    if (p.style.display === "none") {
        p.style.display = "inline";
        m.style.display = "none";
        s.style.display = "none";
    } else {
        p.style.display = "none";
        m.style.display = "inline";
        s.style.display = "table-row";
    }
}
