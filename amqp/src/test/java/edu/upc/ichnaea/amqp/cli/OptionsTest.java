package edu.upc.ichnaea.amqp.cli;

import org.junit.Test;

import static org.junit.Assert.*;

public class OptionsTest {

    enum TestEnum {
        One, Two, Three
    }

    String mStringValue;
    boolean mBooleanValue;
    int mIntegerValue;
    TestEnum mEnumValue;

    Options getOptions() {

        Options options = new Options("exec");
        options.add(new StringOption("string") {
            @Override
            public void setValue(String value) throws InvalidOptionException {
                if (value.equals("invalid")) {
                    throw new InvalidOptionException("Invalid string value");
                }
                mStringValue = value;
            }
        }.setRequired(true));
        options.add(new BooleanOption("boolean") {
            @Override
            public void setValue(boolean value) throws InvalidOptionException {
                mBooleanValue = value;
            }
        });
        options.add(new IntegerOption("integer") {
            @Override
            public void setValue(int value) throws InvalidOptionException {
                mIntegerValue = value;
            }
        });
        options.add(new EnumOption<TestEnum>("enum") {
            @Override
            public void setValue(TestEnum value) throws InvalidOptionException {
                mEnumValue = value;
            }
        }.setDefaultValue(TestEnum.One));
        return options;
    }

    @Test(expected = UnknownOptionException.class)
    public void testUnknownOption() throws OptionException {
        String[] args = { "-d" };
        getOptions().parse(args);
    }

    @Test(expected = MissingOptionException.class)
    public void testMissingOption() throws OptionException {
        String[] args = { "--boolean" };
        getOptions().parse(args);
    }

    @Test
    public void testOptions() throws OptionException {
        mBooleanValue = false;
        mStringValue = "";
        mIntegerValue = 0;
        mEnumValue = TestEnum.One;
        String[] args = { "--boolean", "--string", "paco", "--integer", "5",
                "--enum", "Two" };
        getOptions().parse(args);
        assertTrue(mBooleanValue);
        assertEquals("paco", mStringValue);
        assertEquals(5, mIntegerValue);
        assertEquals(TestEnum.Two, mEnumValue);
    }
}
