package edu.upc.ichnaea.amqp;

import java.util.Arrays;
import java.util.HashMap;
import java.util.Map;

import edu.upc.ichnaea.amqp.app.*;

public class Main {
	
	interface Action {
		public void run(String[] args);
		public String description();
	}
	
	static Map<String,Action> getActions() {
    	Map<String,Action> actions = new HashMap<String,Action>();
    	actions.put("build-models:process", new Action(){
			public void run(String[] args) {
				BuildModelsProcessApp.main(args);
			}
			
			public String description() {
				return "Process incoming build-models requests";
			}
    	});
    	actions.put("build-models:request", new Action(){
			public void run(String[] args) {
				BuildModelsRequestApp.main(args);
			}
			
			public String description() {
				return "Sends a build-models request and waits for the response";
			}
    	});
    	return actions;
	}
	
	private static void exitError(String msg, Map<String,Action> actions) {
		System.out.println(msg);
		System.out.println("Available actions are:");
		for(String key : actions.keySet()) {
			Action action = actions.get(key);
			System.out.println(key+" "+action.description());
		}
        System.exit(1);
	}

    public static void main(String[] args) {
    	Map<String,Action> actions = getActions();
    	
    	if(args.length == 0) {
            exitError("Please specify an action as argument.", actions);
            return;
    	}
    	
    	String actionKey = args[0];
    	String[] actionArgs = Arrays.copyOfRange(args, 1, args.length);


    	if(!actions.containsKey(actionKey)) {
    		exitError("Unknown action "+actionKey+".", actions);
            return;
    	}
    	actions.get(actionKey).run(actionArgs);
    }
}
