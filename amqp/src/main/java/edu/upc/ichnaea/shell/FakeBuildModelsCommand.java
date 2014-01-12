package edu.upc.ichnaea.shell;

public class FakeBuildModelsCommand extends IchnaeaCommand {

    private String mTime;

    public FakeBuildModelsCommand(String time) {
        mTime = time;
    }

    public String getParameters() {
        return "--fake=" + mTime;
    }

}
