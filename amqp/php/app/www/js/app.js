

App = Ember.Application.create({
  LOG_TRANSITIONS: true
});

App.Store = DS.Store.extend({
  revision: 12
});
App.store = App.Store.create();

App.Router.map(function() {
  this.route("build-models", { path: "/" });
});

setInterval(function(){
  App.store.findQuery(App.BuildModelsTask, {});
}, 1000);

App.UploadFileView = Ember.TextField.extend({
    type: 'file',
    attributeBindings: ['name'],
    change: function(evt) {
      var self = this;
      var input = evt.target;
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var fileToUpload = e.srcElement.result;
          self.get('controller').set(self.get('name'), fileToUpload);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
});

App.BuildModelsFormView = Ember.View.extend({
  templateName: 'build-models-form',
  title: 'Build Models',
  submit: function(evt) {
    evt.preventDefault();
    var data = {};
    $.each($(evt.target).serializeArray(), function(_, kv) {
      data[kv.name] = kv.value;
    });
    this.get('controller').send('addTask', data);
  }
});

App.BuildModelsTaskView = Ember.View.extend({
  templateName: 'build-models-task',
  task: null,
  removeItem: function(evt) {
    var task = this.get('task');
    this.get('controller').send('deleteTask', task);
  }
});

App.BuildModelsController = Ember.ArrayController.extend({
  deleteTask: function(task) {
    task.deleteRecord();
    task.store.commit();
  },
  addTask: function(data){
    var self = this;
    data.dataset = this.get('dataset');
    var task = App.BuildModelsTask.createRecord(data);
    task.on("isError", function(msg){
      this.set("error", msg);
    });
    task.store.commit();
  }
});

App.BuildModelsRoute = Ember.Route.extend({
  setupController: function(controller) {
    controller.set("content", App.BuildModelsTask.find());
  }
});

App.BuildModelsTask = DS.Model.extend({
  start: DS.attr('date', { defaultValue: new Date() }),
  end: DS.attr('date'),
  progress: DS.attr('number', { defaultValue: 0 }),
  error: DS.attr('string'),
  season: DS.attr('string'),
  percent: function() {
    return this.get('progress')*100;
  }.property('progress'),
  progressBarStyle: function() {
    return "width: "+this.get('percent')+"%;"
  }.property('percent'),
  state: function() {
    if(this.get('error')) {
      return 'error';
    } else if(this.get('progress')>=1) {
      return 'success';
    } else {
      return '';
    }
  }.property('error', 'percent'),
});
App.BuildModelsTask.toString = function() {
  return "/build-models-task";
};