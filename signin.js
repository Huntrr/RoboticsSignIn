var hydrate = new Resurrect(); //Note this isn't actually Hydrate anymore
var roboList = []; 
//The real code
function Robonaut(name, timein, timeout) {
    this.name = name;
    this.timein = timein;
    this.timeout = timeout;
}
    Robonaut.prototype.getName = function () {
        return $("<div class='name'><input class='name_field' id='" + this.name + "_name' type='text' style='width: 90%;' value='" + this.name + "' /></div>");
    };
    Robonaut.prototype.getIn = function () {
        return $("<div class='in'><input class='in_field' id='" + this.name + "_in' type='time' value='" + this.timein + "' /></div>");
    };
    Robonaut.prototype.getOut = function () {
        if(this.timeout === null) {
            return $("<div class='out'><input class='out_btn' id='" + this.name + "_out' type='button' value='" + "Sign out" + "' /></div>");
        } else {
            return $("<div class='out'><input class='out_field' id='" + this.name + "_out' type='time' value='" + this.timeout + "' /></div>");
        }
    };
    Robonaut.prototype.getX = function () {
        return $("<div class='x'><input class='x_btn' type='button' id='" + this.name + "_x' value='X'></div>");
    };

//var roboList = [new Robonaut("Hunter Lightman", "14:30", "16:00"), new Robonaut("Harrison von Dwingelo", "14:00", "14:15"), new Robonaut("Matt Hushion", "14:07", null)];
var currentEntries = []; //FILL WITH LIST OF NAMES FROM DATABASE
$(document).ready(function () {
    //localStorage.setItem('robos', hydrate.stringify(roboList));
    $(document).on('click', '#logout', function () { if(confirm("Are you sure you want to logout?")) {
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
    } });
    
    
    //populate currentEntries with list of people in the database
    $.ajax({                                      
      url: 'db.php',       
      data: {action: 'getAll'},
      type: 'post',
      dataType: 'json',
      success: function(data)
      {
        currentEntries = [];
        for(var i = 0; i < data.length; i++) {
            currentEntries.push(data[i][0]);
        }
      } 
    });
    
    
    if(localStorage.getItem('robos')) {
       roboList = hydrate.resurrect(localStorage.getItem('robos'));
    }
    
    var populateList = function() {
        var $content = $('#roboList');
        $content.html(' ');
        for (var i = 0; i < roboList.length; i++) {
            $content.append(roboList[i].getName()).append(roboList[i].getIn()).append(roboList[i].getOut()).append(roboList[i].getX());
        }
        
        if(roboList.length < 1) {
            $content.html('<div class="full">No one is signed in yet!</div>')
        }
        
        localStorage.setItem('robos', hydrate.stringify(roboList));
    };
    
    var saveList = function() {
        $('.in_field').each(function() {
            var name = $(this).attr('id').substring(0, $(this).attr('id').length-3);
            var val = $(this).val();
            
            for(var i = 0; i < roboList.length; i++) {
                if(roboList[i].name === name) {
                    roboList[i].timein = val;
                }
            }
        });
        
        $('.out_field').each(function() {
            var name = $(this).attr('id').substring(0, $(this).attr('id').length-4);
            var val = $(this).val();
            
            for(var i = 0; i < roboList.length; i++) {
                if(roboList[i].name === name) {
                    roboList[i].timeout = val;
                }
            }
        });
        
        $('.name_field').each(function() {
            var name = $(this).attr('id').substring(0, $(this).attr('id').length-5);
            var val = $(this).val();
            
            if(name !== val) {
                for(var i = 0; i < roboList.length; i++) {
                    if(roboList[i].name === name) {
                        roboList[i].name = val;
                    }
                }
            }
        });
        
        localStorage.setItem('robos', hydrate.stringify(roboList));
    }
    
    var curTime = function() {
        var date = new Date();
        var hours = date.getHours();
        hours = (hours < 10 ? "0" : "") + parseInt(hours);
        
        var minutes = date.getMinutes();
        minutes = (minutes < 10 ? "0" : "") + parseInt(minutes);
        
        return hours + ":" + minutes;
    }
    
    $(document).on('click', '.x_btn', function () {
        var name = $(this).attr('id').substring(0, $(this).attr('id').length-2);
        
        for(var i = 0; i < roboList.length; i++) {
            if(roboList[i].name === name) {
                roboList.splice(i, 1);
                populateList();
            }
        }
    });
    $(document).on('click', '#initIn', function () {
        if($.inArray($('#input-name').val(), currentEntries) !== -1 || confirm("'" + $('#input-name').val() + "' has never signed in before. Are you sure you want to add a new person?")) {
            roboList.push(new Robonaut($('#input-name').val(), curTime(), null));
            populateList();
        }
        
        $('#input-name').val('');
    });
    $(document).on('click', '.out_btn', function () {
        var name = $(this).attr('id').substring(0, $(this).attr('id').length-4);
        for(var i = 0; i < roboList.length; i++) {
            if(roboList[i].name === name) {
                roboList[i].timeout = curTime();
            }
        }
        
        populateList();
    });
    
    var getTimeDiff = function(start, end) {
        start = start.split(":");
        end = end.split(":");
        
        start = parseInt(start[0])*60 + parseInt(start[1]);
        end = parseInt(end[0])*60 + parseInt(end[1]);
        
        var final = end - start;
        return (typeof final === "number") ? final : 0;
    };
    
    $(document).on('click', '#headhome', function () {
        if(confirm("Are you sure you want to head home? (DON'T CLICK UNTIL WE'RE DONE FOR THE DAY)")) {
            var dayStats = [];
            
            for(var i = 0; i < roboList.length; i++) {
                if(roboList[i].timeout === null) {
                    roboList[i].timeout = curTime();
                }
                dayStats.push([roboList[i].name, getTimeDiff(roboList[i].timein, roboList[i].timeout)]);
            }
            
            $.ajax({                                      
              url: 'db.php',       
              data: {action: 'submitDay', arr: dayStats},
              type: 'post',
              dataType: 'json',
              success: function(data)
              {
                if(data === "DONE") {
                    roboList = [];
                    populateList();
                }
              } 
            });
        }
    });
    
    populateList();
    
    setInterval(function () {saveList();}, 500);
});