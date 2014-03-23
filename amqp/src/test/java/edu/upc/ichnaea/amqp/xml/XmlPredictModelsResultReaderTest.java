package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;

import javax.mail.MessagingException;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.PredictModelsResult;

public class XmlPredictModelsResultReaderTest {

    @Test
    public void testXML() throws SAXException, IOException, MessagingException {
       
		String xml = "<response id=\"jav.PredictModelsRequestClient.9r7jetr7qht925suglah27dt8f\" progress=\"0.0\" ";
		xml += "start=\"2014-03-23T13:26:18.082+0100\" type=\"build_models\"><result name=\"ERROR2\" ";
		xml += "predictedSamples=\"1\" testError=\"0.0\" totalSamples=\"1\"><dataset><column name=\"FC\">";
		xml += "<value>2.2e+07</value></column><column name=\"FE\"><value>1230000</value></column><column name=\"CL\">";
		xml += "<value>240000</value></column><column name=\"SOMCPH\"><value>19300000</value></column>";
		xml += "<column name=\"FRNAPH\"><value>840000</value></column><column name=\"FRNAPH.I\"><value>50400</value>";
		xml += "</column><column name=\"FRNAPH.II\"><value>277200</value></column><column name=\"FRNAPH.III\">";
		xml += "<value>512400</value></column><column name=\"FRNAPH.IV\"><value>0</value></column>";
		xml += "<column name=\"RYC2056\"><value>33500</value></column><column name=\"GA17\"><value>210000</value>";
		xml += "</column><column name=\"HBSA.Y\"><value>NA</value></column><column name=\"HBSA.T\">";
		xml += "<value>NA</value></column><column name=\"CLASS\"><value>1</value></column><column name=\"tHM\">";
		xml += "<value>1</value></column><column name=\"dHM\"><value>1</value></column><column name=\"PREDHM\">";
		xml += "<value>1</value></column><column name=\"tAN\"><value>0</value></column><column name=\"dAN\">";
		xml += "<value>1</value></column><column name=\"PREDAN\"><value>1</value></column><column name=\"PRED\">";
		xml += "<value>1</value></column></dataset><confusionMatrix><column name=\"-1\"><value>0</value>";
		xml += "<value>0</value></column><column name=\"1\"><value>0</value><value>1</value>";
		xml += "</column></confusionMatrix></result></response>";

		PredictModelsResult res = new XmlPredictModelsResultReader().read(xml);
        assertEquals("ERROR2", res.getName());
    }

}
