package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;

import org.custommonkey.xmlunit.Diff;
import org.junit.Test;
import org.xml.sax.SAXException;

import edu.upc.ichnaea.amqp.model.BuildModelsRequest;
import edu.upc.ichnaea.amqp.model.Dataset;
import edu.upc.ichnaea.amqp.model.DatasetColumn;
import edu.upc.ichnaea.amqp.model.DatasetAging;
import edu.upc.ichnaea.amqp.model.DatasetAgingColumn;
import edu.upc.ichnaea.amqp.model.Aging;
import edu.upc.ichnaea.amqp.model.AgingTrial;

public class XmlBuildModelsRequestWriterTest {

    @Test
    public void testXML() throws ParserConfigurationException, SAXException,
            IOException {
        Dataset dataset = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1f);
        column.add(2f);
        column.add(3f);
        dataset.add(column);

        DatasetColumn column2 = new DatasetColumn("test2");
        column2.add(4f);
        column2.add(5f);
        column2.add(6f);
        dataset.add(column2);

        DatasetAging agings = new DatasetAging();

        String id = "455";
        BuildModelsRequest model = new BuildModelsRequest(id, dataset, agings);

        String xml = new XmlBuildModelsRequestWriter().build(model).toString();

        String expectedXml = "<request id=\"455\" type=\"build_models\"><dataset><column name=\"test\">"
                + "<value>1.0</value><value>2.0</value><value>3.0</value></column><column name=\"test2\">"
                + "<value>4.0</value><value>5.0</value><value>6.0</value></column></dataset></request>";

        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }

    @Test
    public void testAgingsXML() throws ParserConfigurationException,
            SAXException, IOException {
        Dataset dataset = new Dataset();
        DatasetColumn column = new DatasetColumn("test");
        column.add(1f);
        column.add(2f);
        column.add(3f);
        dataset.add(column);

        DatasetAging agings = new DatasetAging();
        DatasetAgingColumn agingColumn = new DatasetAgingColumn();
        Aging aging = new Aging();
        AgingTrial trial = new AgingTrial();
        trial.add("0", "4.4");
        trial.add("10", "40");
        trial.add("50", "120.6");
        aging.addTrial(trial);
        agingColumn.put(0.5f, aging);
        agings.put("test", agingColumn);

        String id = "455";
        BuildModelsRequest model = new BuildModelsRequest(id, dataset, agings);

        String xml = new XmlBuildModelsRequestWriter().build(model).toString();

        String expectedXml = "<request id=\"455\" type=\"build_models\"><dataset><column name=\"test\">"
                + "<value>1.0</value><value>2.0</value><value>3.0</value></column></dataset><agings>"
                + "<column name=\"test\"><aging position=\"0.5\"><trial>"
                + "<value key=\"0\">4.4</value><value key=\"10\">40</value>"
                + "<value key=\"50\">120.6</value></trial></aging></column></agings></request>";

        Diff xmlDiff = new Diff(expectedXml, xml);
        assertTrue(xmlDiff.toString(), xmlDiff.similar());
    }
}
