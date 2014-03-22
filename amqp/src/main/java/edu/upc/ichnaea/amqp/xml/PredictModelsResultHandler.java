package edu.upc.ichnaea.amqp.xml;

import org.xml.sax.SAXException;
import org.xml.sax.Attributes;
import org.xml.sax.ContentHandler;
import org.xml.sax.Locator;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class PredictModelsResultHandler implements ContentHandler {

    final static String TAG_RESULT = "result";
    final static String TAG_CONF_MATRIX = "confusionMatrix";
    final static String TAG_VALUE = "value";
    final static String ATTR_NAME = "name";
    final static String ATTR_TEST_ERROR = "testError";
    final static String ATTR_TOTAL_SAMPLES = "totalSamples";
    final static String ATTR_PREDICTED_SAMPLES = "predictedSamples";

    protected DatasetHandler mDatasetHandler;
    protected Dataset mDataset;
    protected StringBuilder mCharacters;
    protected PredictModelsResult mResult;
    protected String mName;
    protected float[][] mConfusionMatrix;
    protected int mConfusionMatrixRow;
    protected int mConfusionMatrixCol;
    protected float mTestError;
    protected int mTotalSamples;
    protected int mPredictedSamples;
    
    public PredictModelsResult getData() {
        return mResult;
    }

    @Override
    public void setDocumentLocator(Locator locator) {
    }

    @Override
    public void startDocument() throws SAXException {
        mName = "";
        mDataset = null;
        mDatasetHandler = null;
        mResult = null;
        mDataset = null;
        mConfusionMatrix = null;
        mConfusionMatrixRow = -1;
        mConfusionMatrixCol = -1;
        mTestError = 0.0f;
        mTotalSamples = 0;
        mPredictedSamples = 0;
        mCharacters = null;
    }

    @Override
    public void startPrefixMapping(String prefix, String uri)
            throws SAXException {
    }

    @Override
    public void endPrefixMapping(String prefix) throws SAXException {
    }

    @Override
    public void startElement(String uri, String localName, String qName,
            Attributes atts) throws SAXException {
        if (localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
            if (mDatasetHandler != null) {
                throw new SAXException(
                        "A dataset cannot be inside another one.");
            }
            mDatasetHandler = new DatasetHandler();
            mDatasetHandler.startDocument();
        }
        if (mDatasetHandler != null) {
            mDatasetHandler.startElement(uri, localName, qName, atts);
        }
        else if(mConfusionMatrixRow >= 0) {
            mCharacters = new StringBuilder();
        } else if (localName.equalsIgnoreCase(TAG_RESULT)) {
            if(atts.getValue(ATTR_NAME) != null) {
                mName = atts.getValue(ATTR_NAME);
            }
            if(atts.getValue(ATTR_TEST_ERROR) != null) {
                try {
                    mTestError = Float.parseFloat(atts.getValue(ATTR_TEST_ERROR));
                }catch(Exception e) {
                }
            }
            if(atts.getValue(ATTR_TOTAL_SAMPLES) != null) {
                try {
                    mTotalSamples = Integer.parseInt(atts.getValue(ATTR_TOTAL_SAMPLES));
                }catch(Exception e) {
                }
            }
            if(atts.getValue(ATTR_PREDICTED_SAMPLES) != null) {
                try {
                    mPredictedSamples = Integer.parseInt(atts.getValue(ATTR_PREDICTED_SAMPLES));
                }
                catch(Exception e) {
                }
            }
        } else if(localName.equalsIgnoreCase(TAG_CONF_MATRIX)) {
            mConfusionMatrix = new float[2][2];
            mConfusionMatrixRow = 0;
            mConfusionMatrixCol = 0;
        }
    }

    @Override
    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.endElement(uri, localName, qName);
            if (localName.equalsIgnoreCase(DatasetHandler.TAG_DATASET)) {
                mDatasetHandler.endDocument();
                mDataset = mDatasetHandler.getDataset();
                mDatasetHandler = null;
            }
        } else if(mConfusionMatrixRow >= 0 && mConfusionMatrixCol >= 0) {
            if(localName.equalsIgnoreCase(TAG_VALUE)) {
                float v = 0.0f;
                try {
                    v = Float.parseFloat(mCharacters.toString());
                }catch(NumberFormatException e) {
                }
                if(mConfusionMatrixRow < mConfusionMatrix.length &&
                    mConfusionMatrixCol < mConfusionMatrix[mConfusionMatrixRow].length) {
                    mConfusionMatrix[mConfusionMatrixRow][mConfusionMatrixCol] = v;
                }             
                mCharacters = null;
                mConfusionMatrixCol++;
                if(mConfusionMatrixCol >= mConfusionMatrix.length) {
                    mConfusionMatrixCol = 0;
                    mConfusionMatrixRow++;
                }
            }
        }
        if(localName.equalsIgnoreCase(TAG_CONF_MATRIX)) {
            mConfusionMatrixRow = -1;
        }
    }

    @Override
    public void characters(char[] ch, int start, int length)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.characters(ch, start, length);
        } else if (mCharacters != null) {
            mCharacters.append(ch, start, length);
        }                
    }

    @Override
    public void ignorableWhitespace(char[] ch, int start, int length)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.ignorableWhitespace(ch, start, length);
        }
    }

    @Override
    public void processingInstruction(String target, String data)
            throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.processingInstruction(target, data);
        }
    }

    @Override
    public void skippedEntity(String name) throws SAXException {
        if (mDatasetHandler != null) {
            mDatasetHandler.skippedEntity(name);
        }        
    }

    @Override
    public void endDocument() throws SAXException {
        if(mDataset != null) {
            mResult = new PredictModelsResult(mName, mDataset, mTotalSamples, mConfusionMatrix, mTestError);
        } else {
            mResult = new PredictModelsResult(mPredictedSamples, mTotalSamples);
        }
    }
}