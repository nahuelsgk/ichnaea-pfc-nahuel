

function BuildModelsTaskFormCtrl($scope, $routeParams, $http) {

  $scope.task = {
    fake_duration: 10,
    fake_interval: 1,
    dataset_format: 'csv',
    aging_format: 'tab',
    aging_filename_format: 'env%column%-%aging%.txt'
  };

  $scope.$on('filesSelected', function(ev, files, contents, name) {
    var reader = new FileReader()
    if(name === "dataset") {
      if(files.length > 0) {
        $scope.task.dataset = contents[0];
      } else {
        delete $scope.task.dataset;
      }
    } else if(name === "aging") {
      $scope.task.aging = {};
      for(var i=0; i<files.length; i++) {
        $scope.task.aging[files[i].name] = contents[i]
      }
    }
  });

  $scope.addTask = function(task) {
    var params = {
      "build-models-task": task
    };
    $http.post("build-models-tasks", params).success(function(data){
      $scope.$emit('buildModelsTaskAdded', data["build-models-task"]);
    }).error(function(data){
      $scope.error = "There was an error sending the request.";
    });
  };

  $scope.addFakeTask = function(data) {
    var task = angular.copy(data);
    task.fake = true;
    this.addTask(task);
  };
}

function BuildModelsTaskListCtrl($scope, $routeParams, $http) {
  $scope.tasks = {};
  var updateTasks = function() {
    $http.get('build-models-tasks').success(function(data) {
      var tasks = data["build-models-tasks"];
      for(var i in tasks) {
        var task = tasks[i];
        if($scope.tasks[task.id] !== undefined) {
          angular.extend($scope.tasks[task.id], task);
        } else {
          $scope.tasks[task.id] = task;
        }
      }
      for(var id in $scope.tasks) {
        var found = false;
        for(var j in tasks) {
          if(tasks[j].id === id) {
            found = true;
            break;
          }
        }
        if(!found) {
          delete $scope.tasks[id];
        }
      }
    });
  };

  setInterval(function(){
    updateTasks();
  }, 1000);
  updateTasks();

  $scope.$on('buildModelsTaskAdded', function() {
    updateTasks();
  });

  $scope.$on('buildModelsTaskRemoved', function() {
    updateTasks();
  });  

  $scope.deleteTask = function(id) {
    $http.delete('build-models-tasks/'+id).success(function(data) {
      $scope.$emit('buildModelsTaskRemoved', id);
    }).error(function(data){
      $scope.error = "There was an error deleting the task.";
    });
  };
}

var app = angular.module('app', []);
app.directive('fileUpload', function () {
  return {
    scope: true,
    link: function ($scope, el, attrs) {
      el.bind('change', function (event) {
        var files = event.target.files;
        var pending = files.length;
        var contents = new Array(files.length);
        for(var i=0; i<files.length; i++) {
          var reader = new FileReader();
          (function (i) { 
            reader.onload = function(e){
              contents[i] = e.target.result;
              pending -= 1;
              if(pending <= 0) {
                $scope.$emit("filesSelected", files, contents, attrs.name);
              }
            };
          })(i);
          reader.readAsText(files[i]);
        }
      });
    }
  };
});