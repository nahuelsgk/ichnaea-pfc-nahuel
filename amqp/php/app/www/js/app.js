

function BuildModelsTaskFormCtrl($scope, $routeParams, $http) {

  $scope.task = {
    fake_duration: 10,
    fake_interval: 1
  };

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

  $scope.$on('buildModelsTaskAdded', function(task) {
    debugger;
    updateTasks();
  });

  $scope.$on('buildModelsTaskRemoved', function(id) {
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

angular.module('my', [])
  .directive('MyPreventSubmit', function(scope, element) {
    element.on('submit', function(event) {
      debugger;
      event.preventDefault();
    });
  });