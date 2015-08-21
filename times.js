//The real code
function Robonaut(name, totalTime, seasonTime) {
    this.name = name;
    this.totalTime = totalTime;
    this.seasonTime = seasonTime;
}
    Robonaut.prototype.hours = function () {
        return Math.floor(this.totalTime/60);
    };
    Robonaut.prototype.minutes = function() {
        return this.totalTime - 60*this.hours();
    };
    Robonaut.prototype.s_hours = function () {
        return Math.floor(this.seasonTime/60);
    };
    Robonaut.prototype.s_minutes = function() {
        return this.seasonTime - 60*this.s_hours();
    };
    Robonaut.prototype.getName = function () {
        return $("<div class='name'><span class='name_field' id='" + this.name + "_name' style='width: 90%;'>" + this.name + "</span></div>");
    };
    Robonaut.prototype.getTotalHours = function () {
        var editable = $('#admin').html() === "TRUE";
        //return $("<div class='totalHours'><input class='hours_field' id='" + this.name + "_hours' type='text' style='width: 30px;' value='" + this.hours() + "' " + (editable ? "" : "disabled='true' readonly='true'") + " /> hours, <input class='minutes_field' id='" + this.name + "_minutes' type='text' style='width: 20px;' maxlength='2' value='" + this.minutes() + "' " + (editable ? "" : "disabled='true' readonly='true'") + " /> minutes</div>");
        return $("<div class='totalHours'>Season: " + this.s_hours() + " hours, "  + this.s_minutes() + " minutes<br>Total: " + this.hours() + " hours, "  + this.minutes() + "<br>---</div>");
    };
    Robonaut.prototype.getX = function () {
        var editable = $('#admin').html() === "TRUE";
        return $("<div class='x'><input class='x_btn' type='button' id='" + this.name + "_x" + "'value='X' " + (editable ? "" : "disabled='true'") + "></div>");
    };

var roboList;
var currentEntries = []; //FILL WITH LIST OF NAMES FROM DATABASE
var editable = $('#admin').html() === "TRUE";
$(document).ready(function () {
    $(document).on('click', '#logout', function () {
        $.ajax({
            url: 'db.php',
            data: {action: 'logout'},
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if(data === "DONE") {
                    window.location.reload(true);
                }
            }
        });
    })
    
    var loadList = function() { 
        //Will load roboList from db
        //alert('loading');
        $.ajax({                                      
          url: 'db.php',       
          data: {action: 'loadList'},
          type: 'post',
          dataType: 'json',
          success: function(data)
          {
              //alert("got it");
            roboList = [];
            for(var i = 0; i < data.length; i++) {
                roboList.push(new Robonaut(data[i][0], data[i][1], data[i][2]));
            }
        
            populateList();
          } 
        });
        
        
    };
    
    var populateList = function() {
        var $content = $('#roboList');
        $content.html(' ');
        for (var i = 0; i < roboList.length; i++) {
            $content.append(roboList[i].getName()).append(roboList[i].getTotalHours()).append(roboList[i].getX());
        }
    };
    
    /*var saveList = function() {
        $('.hours_field').each(function() {
            var name = $(this).attr('id').substring(0, $(this).attr('id').length-6);
            var val = $(this).val();
            
            for(var i = 0; i < roboList.length; i++) {
                if(roboList[i].name === name) {
                    roboList[i].totalTime = (typeof parseInt(val) === "number") ? parseInt(val)*60 : 0;
                }
            }
        });
        
        $('.minutes_field').each(function() {
            var name = $(this).attr('id').substring(0, $(this).attr('id').length-8);
            var val = $(this).val();
            
            for(var i = 0; i < roboList.length; i++) {
                if(roboList[i].name === name) {
                    roboList[i].totalTime = roboList[i].totalTime + ((typeof parseInt(val) === "number") ? parseInt(val) : 0);
                }
            }
        });
    }*/
    
    $(document).on('click', '.x_btn', function () {
        var name = $(this).attr('id').substring(0, $(this).attr('id').length-2);
        
        $.ajax({                                      
          url: 'db.php',       
          data: {action: 'remove', val:name},
          type: 'post',
          dataType: 'json',
          success: function(data)
          {
            if(data === "DONE") {
                for(var i = 0; i < roboList.length; i++) {
                    if(roboList[i].name === name) {
                        roboList.splice(i, 1);
                        populateList();
                    }
                }
            }
          } 
        });
    });
    /*$(document).on('click', '#update', function () {
        saveList();
        
        var array = [];
        
        for(var i = 0; i < roboList.length; i++) {
            array.push([roboList[i].name, roboList[i].totalTime]);
        }
        
        $.ajax({                                      
          url: 'db.php',       
          data: {action: 'update', arr:array},
          type: 'post',
          dataType: 'json',
          success: function(data)
          {
            if(data === "DONE") {
                generateList();
            }
          } 
        });
        
        
    });*/
    
    var parseTime = function(time) {
        time = time.split(":");
        var total = parseInt(time[0])*60 + parseInt(time[1]);
        
        return (typeof total === "number") ? total : 0;
    };
    
    /*$(document).on('click', '#add', function () {
        //Add a new name
        var array = [$('#input-name').val(), parseTime($('#time').val())];
        $.ajax({                                      
          url: 'db.php',       
          data: {action: 'add', arr:array},
          type: 'post',
          dataType: 'json',
          success: function(data)
          {
            if(data === "DONE") {
                $('#input-name').val('');
                $('#time').val('');
                
                generateList();
            }
          } 
        });
        
        
    });*/
    
    var generateList = function () {
        //TODO: Call loadList via ajax
        loadList();
    }
    
    generateList();
});