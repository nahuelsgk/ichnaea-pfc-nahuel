 
package edu.upc.ichnaea.shell;

import java.text.SimpleDateFormat;
import java.text.ParseException;

import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public abstract class UpdateProgressCommandReader extends CommandReader {

    protected SimpleDateFormat mTimeFormat;
    protected Pattern mRegexPercent;
    protected Pattern mRegexEndTime;
    protected float mPercent = 0;
    protected Calendar mEnd = null;

    public UpdateProgressCommandReader(CommandResultInterface result, boolean verbose) {
        this(result, "EEE MMM dd HH:mm:ss z yyyy", "(\\d*)%", "^ *finish: *(.*) *$", verbose);
    }

    public UpdateProgressCommandReader(CommandResultInterface result, String timeFormat, String regexPercent, String regexEndTime, boolean verbose) {
        super(result, verbose);
        mRegexPercent = Pattern.compile(regexPercent);
        mRegexEndTime = Pattern.compile(regexEndTime);
        mTimeFormat = new SimpleDateFormat(timeFormat);
    }

    protected void onLineRead(String line) {
        boolean updated = false;
        Matcher m = mRegexPercent.matcher(line);
        if (m.find()) {
            try {
                mPercent = Float.parseFloat(m.group(1));
            } catch (NumberFormatException e) {
                mPercent = 0;
            }
            if (mPercent > 1) {
                mPercent /= 100;
            }
            updated = true;
        }
        m = mRegexEndTime.matcher(line);
        if (m.find()) {
            mEnd = Calendar.getInstance();
            try {
                mEnd.setTime(mTimeFormat.parse(m.group(1)));
            } catch (ParseException e) {
            }
            updated = true;
        }
        if (updated) {
            onUpdate(mPercent, mEnd);
        }
    }

    protected abstract void onUpdate(float percent, Calendar end);
};

    