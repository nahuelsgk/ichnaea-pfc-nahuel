package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;
import java.util.Collection;

import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;

public class XmlDatasetReaderTest {

    @Test
    public void testXML() throws SAXException, IOException {
        String xml = "<dataset>\n";
        xml += "<column name=\"test\"><value>1</value><value>2</value><value>3</value></column>\n";
        xml += "<column name=\"test2\"><value>3</value><value>4</value></column>\n";
        xml += "<column name=\"test3\"><value>5</value><value>6</value><value>7</value></column>\n";
        xml += "</dataset>";

        Dataset dataset = new XmlDatasetReader().read(xml);

        Collection<String> names = dataset.columnNames();
        assertTrue(names.contains("test"));
        assertEquals(3, names.size());

        DatasetColumn column = dataset.get("test");

        assertEquals(1, column.get(0).intValue());
        assertEquals(2, column.get(1).intValue());
        assertEquals(3, column.get(2).intValue());

        column = dataset.get("test2");
        assertEquals(3, column.get(0).intValue());
        assertEquals(4, column.get(1).intValue());
        assertEquals(2, column.size());

    }

}
