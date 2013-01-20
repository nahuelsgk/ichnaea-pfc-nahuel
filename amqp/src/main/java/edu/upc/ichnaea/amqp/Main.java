package edu.upc.ichnaea.amqp;

import java.util.Arrays;
import java.util.HashMap;
import java.util.Map;

import edu.upc.ichnaea.amqp.app.*;

public class Main {
	
	interface Action {
		public void run(String[] args);
	}
	
	static Map<String,Action> getActions() {
    	Map<String,Action> actions = new HashMap<String,Action>();
    	actions.put("build-models:process", new Action(){
			@Override
			public void run(String[] args) {
				BuildModelsProcessApp.main(args);
			}
    	});
    	actions.put("build-models:request", new Action(){
			@Override
			public void run(String[] args) {
				BuildModelsRequestApp.main(args);
			}
    	});
    	return actions;
	}
	
	private static void exitError(String msg) {
		System.out.println(msg);
        System.exit(1);
	}

    public static void main(String[] args) {
    	if(args.length == 0) {
            exitError("Please specify an action as argument.");
            return;
    	}
    	
    	String actionKey = args[0];
    	String[] actionArgs = Arrays.copyOfRange(args, 1, args.length);
    	Map<String,Action> actions = getActions();

    	if(!actions.containsKey(actionKey)) {
    		String appKeys = StringUtils.join(actions.keySet(), ", ");
    		exitError("Unknown action "+actionKey+". Available actions are: "+appKeys);
            return;
    	}
    	actions.get(actionKey).run(actionArgs);
    }
}
