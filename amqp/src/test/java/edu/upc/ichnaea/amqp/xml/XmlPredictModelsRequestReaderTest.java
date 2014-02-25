package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import java.util.Collection;

import javax.mail.MessagingException;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;
import edu.upc.ichnaea.amqp.model.PredictModelsRequest;

public class XmlPredictModelsRequestReaderTest {

    @Test
    public void testXML() throws SAXException, IOException, MessagingException {
        String xml = "<request id=\"432\" type=\"predict_models\"><dataset>\n";
        xml += "<column name=\"test\"><value>1.5</value><value>2</value><value>3</value></column>\n";
        xml += "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
        xml += "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";
        xml += "</dataset></request>";

        PredictModelsRequest message = new XmlPredictModelsRequestReader()
                .read(xml);

        Dataset dataset = message.getDataset();

        Collection<String> names = dataset.columnNames();
        assertTrue(names.contains("test"));
        assertEquals(3, names.size());

        DatasetColumn column = dataset.get("test");

        assertTrue(1.5 == column.get(0).floatValue());

        column = dataset.get("test2");
        assertEquals(2, column.size());
    }

}
