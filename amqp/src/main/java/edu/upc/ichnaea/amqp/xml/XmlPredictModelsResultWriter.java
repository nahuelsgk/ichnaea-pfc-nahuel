package edu.upc.ichnaea.amqp.xml;

import javax.xml.parsers.ParserConfigurationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class XmlPredictModelsResultWriter extends XmlWriter {

    public XmlPredictModelsResultWriter() throws ParserConfigurationException {
    	super(PredictModelsResultHandler.TAG_RESULT);
    }
    
    public XmlPredictModelsResultWriter(Document doc, Element root) {
        super(doc, root);
    }

    public XmlPredictModelsResultWriter(Document doc) {
        super(doc, PredictModelsResultHandler.TAG_RESULT);
    }

    public XmlPredictModelsResultWriter build(PredictModelsResult result) {

		Element root = getRoot();

        root.setAttribute(PredictModelsResultHandler.ATTR_TOTAL_SAMPLES,
                 String.valueOf(result.getTotalSamples()));
        root.setAttribute(PredictModelsResultHandler.ATTR_PREDICTED_SAMPLES,
                 String.valueOf(result.getPredictedSamples()));

        if(result.isFinished()) {
            root.setAttribute(PredictModelsResultHandler.ATTR_NAME,
                String.valueOf(result.getName()));
            root.setAttribute(PredictModelsResultHandler.ATTR_TEST_ERROR,
                String.valueOf(result.getTestError()));

    		if (!result.getDataset().isEmpty()) {
                Element xmlDataset = appendChild(DatasetHandler.TAG_DATASET);
                new XmlDatasetWriter(getDocument(), xmlDataset).build(result
                        .getDataset());
            }

    		Element xmlConf = appendChild(PredictModelsResultHandler.TAG_CONF_MATRIX);
            float[][] confMatrix = result.getConfusionMatrix();
            for(int i = 0; i < confMatrix.length; i++) {
            	for(int j = 0; j < confMatrix[i].length; j++) {
                    Element xmlCell = createElement(PredictModelsResultHandler.TAG_VALUE);
                    xmlCell.setTextContent(String.valueOf(confMatrix[i][j]));
                    xmlConf.appendChild(xmlCell);
            	}
            }
        }

        return this;
    }

}