

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
  submitReal: function(evt) {
    var elm = $(this.get('element'));
    var data = {
      season: elm.find('#build-models-form-season').val(),
      section: elm.find('#build-models-form-section').val(),
    };
    this.get('controller').send('addTask', data);
  },
  submitFake: function() {
    var elm = $(this.get('element'));
    var data = {
      fake_duration: elm.find('#build-models-form-fake-duration').val(),
      fake_interval: elm.find('#build-models-form-fake-interval').val(),
    };
    this.get('controller').send('addTask', data);
  }
});

App.BuildModelsTasksView = Ember.CollectionView.extend({
  tagName: 'tbody',
  itemViewClass: 'App.BuildModelsTaskView',  
  createChildView: function(viewClass, attrs) {
    attrs.task = attrs.content;
    return this._super(viewClass, attrs);
  }
});

App.BuildModelsTaskView = Ember.View.extend({
  templateName: 'build-models-task',
  classNameBindings: ['rowClass'],
  task: null,
  taskId: function() {
    return this.get('task.id');
  }.property('task.id'),
  taskError: function() {
    return this.get('task.error');
  }.property('task.error'),    
  startTime: function() {
    return this.get('task.start');
  }.property('task.start'),
  endTime: function() {
    return this.get('task.end');
  }.property('task.end'),
  percent: function() {
    return this.get('task.progress')*100;
  }.property('task.progress'),  
  barStyle: function() {
    return "width: "+this.get('percent')+"%;";
  }.property('percent'),
  rowClass: function() {
    if(this.get('task.error')) {
      return 'error';
    } else if(this.get('task.progress')>=1) {
      return 'success';
    } else {
      return '';
    }
  }.property('task.error', 'task.progress'),
  didInsertElement: function() {
    var elm = $(this.get('element'));
    elm.find('.has-tooltip').tooltip();
  },
  removeItem: function(evt) {
    var task = this.get('task');
    this.get('controller').send('deleteTask', task);
  }
});

App.BuildModelsController = Ember.Controller.extend({
  tasks: function() {
    return App.BuildModelsTask.find();
  }.property().volatile(),
  deleteTask: function(task) {
    task.deleteRecord();
    App.store.commit();
  },
  addTask: function(data){
    var self = this;
    data.dataset = this.get('dataset');
    var task = App.BuildModelsTask.createRecord(data);
    task.on("isError", function(msg){
      this.set("error", msg);
    });
    App.store.commit();
  }
});

App.BuildModelsTask = DS.Model.extend({
  start: DS.attr('date'),
  end: DS.attr('date'),
  progress: DS.attr('number', { defaultValue: 0 }),
  error: DS.attr('string'),
  season: DS.attr('string'),
  fake_duration: DS.attr('number'),
  fake_interval: DS.attr('number')
});
App.BuildModelsTask.toString = function() {
  return "/build-models-task";
};