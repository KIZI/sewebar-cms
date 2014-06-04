/**
 * Created by Stanislav on 22.5.14.
 */
google.load("visualization", "1", {packages:["corechart"]});

function googlePieChart(elementId,data){

    google.setOnLoadCallback(function(){paintChart(elementId,data);});
    function paintChart(elementId,data){
        var data = google.visualization.arrayToDataTable(data);

        var options = {
            title: "",
            pieHole: 0
        };

        var chart = new google.visualization.PieChart(document.getElementById("resultsChart"));
        chart.draw(data, options);
    };
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function ModelTester(args){
    this.contentElementId=(args.contentElementId?args.contentElementId:"");
    this.infoElementId=(args.infoElementId?args.infoElementId:"");
    this.rulesExportUrl=(args.rulesExportUrl?args.rulesExportUrl:"");
    this.removeRuleUrl=(args.removeRuleUrl?args.removeRuleUrl:"");
    this.testUrl=(args.testUrl?args.testUrl:"");

    this.runTest=function(){
        if (this.rulesExportUrl!=""){
            //prepare XML
            this.prepareRulesXmlFile();
        }else if(this.testUrl!=""){
            //run test
            this.testFiles();
        }else{
            this.showResults();
        }
    }

    this.drawChart=function(elementId,data){alert("draw");
        paintChart(resultsChart,[]);

    }



    this.showResults=function(){
        this.showStatus("");
        var contentHtml="";
        if (this.jsonContent.rowsCount>0){
            contentHtml ="<table class=\"resultsTable hiddenLinks\">";
            contentHtml+="<tr><td>True positive:</td><td><strong>"+this.jsonContent.truePositive+"</strong></td></tr>";
            contentHtml+="<tr><td>False positive:</td><td><strong>"+this.jsonContent.falsePositive+"</strong></td></tr>";
            contentHtml+="<tr><td>Test rows count:</td><td><strong>"+this.jsonContent.rowsCount+"</strong></td></tr>";
            contentHtml+="<tr><td colspan=\"2\"></td></tr>";
            contentHtml+="<tr><td>Precision:</td><td><strong>"+(Math.round(1000*((this.jsonContent.truePositive*1)/((this.jsonContent.truePositive*1)+(this.jsonContent.falsePositive*1))))/10)+"%</strong></td></tr>";
            contentHtml+="<tr><td>Recall:</td><td><strong>"+(Math.round(1000*((this.jsonContent.truePositive*1)/(this.jsonContent.rowsCount*1)))/10)+"%</strong></td></tr>";
            contentHtml+="</table>";

            contentHtml+="<div id=\"resultsChart\" ></div>";

            if (this.jsonContent.rules && (this.jsonContent.rules.length>0)){
                contentHtml+="<table class=\"rulesTable\"><tr><th class=\"text\">Rule</th><th>True positive</th><th>False positive</th></tr>";
                for (var i=0;i<this.jsonContent.rules.length;i++){
                    var rule=this.jsonContent.rules[i];
                    contentHtml+="<tr><td>"+rule.text;
                    if (this.removeRuleUrl){
                        contentHtml+="<a href="+this.removeRuleUrl.replace("{ruleId}",rule.id)+" class=\"remove\"></a>";
                    }
                    contentHtml+="</td><td class=\"right\">"+rule.truePositive;
                    contentHtml+="</td><td class=\"right\">"+rule.falsePositive+"</td></tr>";
                }
                contentHtml+="</table>";
            }


            $(this.contentElementId).set("html",contentHtml);
//                  var chartData = ;
//                  console.log(chartData);
            googlePieChart("resultsChart",[
                ["Result","Rows count"],
                ["True positive",this.jsonContent.truePositive*1],
                ["False positive",this.jsonContent.falsePositive*1],
                ["False negative",(this.jsonContent.rowsCount-this.jsonContent.truePositive-this.jsonContent.falsePositive)]
            ]);


        }else{
            contentHtml+="<p class=\"error\">Testing failed.</p>";
            $(this.contentElementId).set("html",contentHtml);
        }
    }

    this.showStatus=function(status){
        if (status==""){
            $(this.infoElementId).hide();
        }else{
            $(this.infoElementId).set('html',status);
            $(this.infoElementId).show();
        }
    }

    this.testFiles=function(){
        this.showStatus("Testing...");
        new Request.JSON({
            url:this.testUrl,
            onComplete: function(jsonContent){
                this.testUrl="";
                this.jsonContent=jsonContent;
                this.runTest();
            }.bind(this)
        }).send();
    }

    this.prepareRulesXmlFile=function(){
        this.showStatus("Exporting association rules...");
        new Request.HTML({
            url: this.rulesExportUrl,
            onSuccess: function(responseText){
                this.rulesExportUrl="";
                this.runTest();

            }.bind(this),
            onFailure: function(){
                alert("failure");
            }

        }).send();
    }



}