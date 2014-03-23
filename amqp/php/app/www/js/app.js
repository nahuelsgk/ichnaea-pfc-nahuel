

function BuildModelsTaskFormCtrl($scope, $routeParams, $http) {

  $scope.task = {
    type: 'build-models',
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
      "task": task
    };
    $http.post("tasks", params).success(function(data){
      $scope.$emit('taskAdded', data["task"]);
    }).error(function(data){
      $scope.error = "There was an error sending the build-models request.";
    });
  };

}

function PredictModelsTaskFormCtrl($scope, $routeParams, $http) {

  $scope.task = {
    type: 'predict-models',
    dataset_format: 'csv',
  };

  $scope.$on('filesSelected', function(ev, files, contents, name) {
    var reader = new FileReader()
    if(name === "dataset") {
      if(files.length > 0) {
        $scope.task.dataset = contents[0];
      } else {
        delete $scope.task.dataset;
      }
    }
    if(name === "data") {
      if(files.length > 0) {
        $scope.task.data = btoa(contents[0]);
      } else {
        delete $scope.task.data;
      }
    }
  });

  $scope.addTask = function(task) {
    var params = {
      "task": task
    };
    $http.post("tasks", params).success(function(data){
      $scope.$emit('taskAdded', data["task"]);
    }).error(function(data){
      $scope.error = "There was an error sending the predict-models request.";
    });
  };

}

function FakeTaskFormCtrl($scope, $routeParams, $http) {

  $scope.task = {
    type: 'fake',
    duration: 10,
    interval: 1,
  };

  $scope.addTask = function(task) {
    var params = {
      "task": task
    };
    $http.post("tasks", params).success(function(data){
      $scope.$emit('taskAdded', data["task"]);
    }).error(function(data){
      $scope.error = "There was an error sending the fake request.";
    });
  };

}

function TaskListCtrl($scope, $routeParams, $http) {
  
  $scope.tasks = {};
  $scope.updating = true;  

  var updateTasks = function() {
    if(!$scope.updating) {
      return;
    }
    $http.get('tasks').success(function(data) {
      var tasks = data["tasks"];
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
    }).error(function(){
      $scope.updating = false;
    });
  };

  setInterval(function(){
    updateTasks();
  }, 1000);
  updateTasks();

  $scope.$on('TaskAdded', function() {
    updateTasks();
  });

  $scope.$on('TaskRemoved', function() {
    updateTasks();
  });
  
  $scope.retryUpdate = function() {
  	$scope.updating = true;
  };  

  $scope.deleteTask = function(id) {
    $http.delete('tasks/'+id).success(function(data) {
      $scope.$emit('TaskRemoved', id);
    }).error(function(data){
      $scope.error = "There was an error deleting the task.";
    });
  };

   $scope.viewResult = function(id) {
    console.log("lalala");
    $(this).find(".task-result").modal("show");
   }
}

var app = angular.module('app', ['ngSanitize']);
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
          // reader.readAsText(files[i]);
          reader.readAsBinaryString(files[i]);
        }
      });
    }
  };
});