package edu.upc.ichnaea.shell;

public class FakeCommand extends IchnaeaCommand {

    private float mDuration;
    private float mInterval;

    public FakeCommand(float duration, float interval) {
        mDuration = duration;
        mInterval = interval;
    }

    public String getParameters() {
        return " fake " + mDuration + ":" + mInterval;
    }

}
