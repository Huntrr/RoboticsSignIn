//The real code
function Robonaut(name, totalTime, seasonTime, timeLog) {
    this.name = name;
    this.totalTime = totalTime;
    this.timeLog = timeLog;
    this.seasonTime = seasonTime
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
        return $("<div class='name'>" + this.name + "</div>");
    };
    Robonaut.prototype.getTotalHours = function () {
        return $("<div class='totalHours'>" + "This Season: " + this.s_hours() + " hours, " + this.s_minutes() + " minutes<br>" + "Total: " + this.hours() + " hours, " + this.minutes() + " minutes<br><br>" + this.getTimeLog() + "<br><br></div>");
    };
    Robonaut.prototype.getTimeLog = function () {
        var log = "";
        for(var i = 0; i < this.timeLog.length; i++) {
            var entry = this.timeLog[i];
            log = log + entry.getText();
        }
        return log;
    };

function Entry(date, totalTime) {
    this.date = new Date(date);
    this.totalTime = totalTime;
}
    Entry.prototype.hours = function () {
        return Math.floor(this.totalTime/60);
    };
    Entry.prototype.minutes = function() {
        return this.totalTime - 60*this.hours();
    };
    Entry.prototype.getDate = function() {
        return "" + (this.date.getMonth() + 1) + "/" + this.date.getDate() + "/" + this.date.getFullYear()
    };
    Entry.prototype.getTotalTime = function () {
        return "" + this.hours() + " hours, " + this.minutes() + " minutes";
    };
    Entry.prototype.getText = function () {
        return "" + this.getDate() + " - " + this.getTotalTime() + "<br>";
    };

var roboList;
var currentEntries = []; //FILL WITH LIST OF NAMES FROM DATABASE
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
        $.ajax({                                      
          url: 'db.php',       
          data: {action: 'loadUserList'},
          type: 'post',
          dataType: 'json',
          success: function(data)
          {
            roboList = [];
            for(var i = 0; i < data.length; i++) {
                var entries = [];
                for(var j = 0; j < data[i][3].length; j++) {
                    entries.push(new Entry(data[i][3][j][0], data[i][3][j][1]));
                }
                roboList.push(new Robonaut(data[i][0], data[i][1], data[i][2], entries));
            }
        
            populateList();
          } 
        });
        
        
    };
    
    var populateList = function() {
        var $content = $('#roboList');
        $content.html(' ');
        for (var i = 0; i < roboList.length; i++) {
            $content.append(roboList[i].getName()).append(roboList[i].getTotalHours());
        }
    };
    
    var parseTime = function(time) {
        time = time.split(":");
        var total = parseInt(time[0])*60 + parseInt(time[1]);
        
        return (typeof total === "number") ? total : 0;
    };
    
    var generateList = function () {
        //TODO: Call loadList via ajax
        loadList();
    }
    
    generateList();
});