
var fulldata;
var index;
var idata=0;

$(document).ready(function() {
    $('#plotfile').hide();
    $('#subplot').hide();

    $("#type").on('change',load_index);

    $('#plotfile').append("<option>None</option>");

    $("#plotfile").on('change',function (){
        $('#mainplot').fadeTo(200,0.5);
        $('#subplot').empty();
        $('#subplot').hide();
        $('#subplot_load').show();
        var uu="get_data.php?type="+$("#type").val()+"&id="+$('#plotfile').val()+"&o="+$("#observer").val();
        $.get(uu, success=load_data);
    });

    $('#subplot').on('change',plot);


    load_observers();
});

function load_observers() {
    var uu="list_observers.php";

    $.get(uu,function (data) {
        //alert(data);
        //data = JSON.parse(data);
        var sel = $('#observer');
        sel.empty();
        sel.append("<option value='' selected disabled hidden>Select Observer ID</option>");
        for (f in data) {
            f=data[f];
            sel.append("<option value='"+f+"'>"+f+"</option>");
        }
        sel.append("<option value='-NONE-'>*No Observer*</option>");
        $("#observer").on('change',load_index);
        $("#observer").prop( "disabled", false);
    });

}
function load_index() {
    $("#type").prop( "disabled", false);
    $('#subplot').hide();
    $('#subplot_load').show();
    $('#subplot').empty();
    $('#plotfile').empty();
    $('#plotfile').hide();

    var uu="list_data.php?type="+$("#type").val()+"&o="+$("#observer").val();

    $.get(uu,function (data) {
        //alert(data);
        //data = JSON.parse(data);
        var sel = $('#plotfile');
        sel.empty();
        for (f in data) {
            f=data[f];
            sel.append("<option value='"+f+"'>"+f+"</option>");
        }
        $('#plotfile').show();
        $('#plotfile').change();
    });

}

function load_data(data) {
    fulldata = JSON.parse(data);
    $('#subplot').empty();
    for (i in fulldata){
        name=fulldata[i]['SOURCE']+" &mdash; "+fulldata[i]['DATE-OBS']+" "+fulldata[i]['LST'];
        $('#subplot').append("<option value='"+i+"'>"+name+"</option>");
    }
    $('#subplot_load').hide();
    $('#subplot').show();
    plot();
}

function plot() {
    $('#mainplot').fadeTo(200,0.5);
    if ($("#type").val()=="scan") plotscan();
    else plotspec();
    $('#mainplot').fadeTo(200,1.0);
}
function plotspec() {
    idata = parseInt($('#subplot').val());
    data = fulldata[idata];

    name=data['SOURCE']+" "+data['DATE-OBS']+" "+data['LST'];
    var ctr_vel = data['V'];
    var nchan   = data['NCH'];
    var dvel    = data['DV'];
    var v0  = ctr_vel - dvel*nchan/2.0 - dvel/2.0;

    var vel=[];
    var i;
    for (i=0; i < nchan ; i++) {
        vel.push(v0 + i*dvel);
    }


    var pdata = [{ x: vel, y: data['DATA'] }];
    var layout={ title: name,
        xaxis: {title: "Velocity (km/s)", showline:true},
        yaxis: {title: "Temparature (K)", showline:true}
    };
    var config={responsive:true};

    Plotly.newPlot( 'mainplot', pdata,layout,config );

    var dlurl="download_data.php?type="+$("#type").val()+"&id="+$('#plotfile').val()+"&c="+idata+"&o="+$("#observer").val();
;
    $('#csvdownload').attr('href',dlurl);
}

function plotscan() {
    idata = parseInt($('#subplot').val());
    data = fulldata[idata];

    name=data['SOURCE']+" "+data['DATE-OBS']+" "+data['LST'];


    var pdata = [{ x: data['XVAL'], y: data['DATA'] }];
    var layout={ title: name,
        xaxis: {title: "Scan Offset (deg)", showline:true},
        yaxis: {title: "Flux Density (System Counts)", showline:true}
    };
    var config={responsive:true};

    Plotly.newPlot( 'mainplot', pdata,layout,config );

    var dlurl="download_data.php?type="+$("#type").val()+"&id="+$('#plotfile').val()+"&c="+idata+"&o="+$("#observer").val();
;
    $('#csvdownload').attr('href',dlurl);
}
