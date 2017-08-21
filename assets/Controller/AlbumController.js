angular.module("fbalbum", []).controller("albumController", function ($window, $scope, $http) {
    $scope.selectedalbum = [];
    $scope.file = {};
    $scope.file.filename=null;
    $scope.file.folder=null;
    $scope.sharestate = true;
    $scope.isalbum = [];
    $scope.albumselected = 0;
    var i=0;
    $scope.allalbumjson=$window.albumjson;
    $scope.loadimage=function(loadurl){
         HoldOn.open({
            theme: 'sk-cube',
            message: "<h4>" + " Loading album images</h4>"
        });
        $("#albums").load(loadurl, function(responseTxt, statusTxt, jqXHR){
			console.log(responseTxt);
            if(statusTxt == "success"){
                HoldOn.close();
            }
            if(statusTxt == "error"){
                alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
            }
        });
    };
    $scope.addalbum = function (id, albumname, albumid) {
        if ($scope.isalbum[id] == true) {
            $scope.selectedalbum.push({"useralbumid": albumid + "", "useralbumname": albumname});
            $scope.albumselected += 1;
        } else {
            for (i = 0; i < $scope.selectedalbum.length; i++) {
                if ($scope.selectedalbum[i].useralbumid == albumid) {
                    $scope.selectedalbum.splice(i, 1);
                }
            }
            $scope.albumselected -= 1;
        }
        if ($scope.albumselected > 0) {
            $scope.sharestate = false;
            document.getElementById("downloadmultiple").disabled = false;
        }
        else {
            $scope.sharestate = true;
            document.getElementById("downloadmultiple").disabled = true;
            document.getElementById("sharemultiple").disabled = true;
        }
    };
    $scope.singledownload = function (albumname, albumid,type) {
        if(type==1){
            $scope.downloadalbum({data: [{"useralbumid": albumid + "", "useralbumname": albumname}]});
        }
        else{
            $scope.sharealbum({data: [{"useralbumid": albumid + "", "useralbumname": albumname}]});
        }
    };
    $scope.download_Multiple_Album = function (type) {
        if(type==1){
            $scope.downloadalbum({data: $scope.selectedalbum});
        }else{
            $scope.sharealbum({data: $scope.selectedalbum});
        }
    };
    $scope.download_All_Album = function (type) {
       
        for (var i in $scope.allalbumjson) {
            $scope.selectedalbum.push({
                "useralbumid": $scope.allalbumjson[i]['id'] + "",
                "useralbumname": $scope.allalbumjson[i]['name']
            });
        } 
        if(type==1){
            $scope.downloadalbum({data: $scope.selectedalbum});
        }else{
            $scope.sharealbum({data: $scope.selectedalbum});
        }
    };
    $scope.downloadfolder = function (foldername) {
        $window.location = foldername;
    };
    $scope.sharealbum = function (data) {
        HoldOn.open({
            theme: 'sk-cube',
            message: "<h4>" + "Moving your album to drive</h4>"
        });
        $http({
            method: "post", url: "shareInDrive.php", data: data,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            console.log(result);    
            HoldOn.close();
            document.getElementById("infotouser").innerHTML = "Your album is successfully uploaded in your google drive";
            $('#gshareModal').modal('show');
        }, function (reason) {
        });
    };
    $scope.downloadalbum = function (data) {
        HoldOn.open({
            theme: 'sk-cube',
            message: "<h4>" + " Preparing your zip file</h4>"
        });
        $http({
            method: "post", url: "zipDownload.php", data: data,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            HoldOn.close();
            $scope.file.filename = result.data.split("/")[3].toString();
            $scope.file.folder = result.data;
            document.getElementById("filename").innerHTML = result.data.split("/")[3];
            $('#exampleModal').modal('show');
        }, function (reason) {
        });
    };
    $scope.googleauth = function (url) {
        $window.location = url;
    };
});