package edu.upc.ichnaea.amqp.xml;

import static org.junit.Assert.*;

import java.util.ArrayList;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;

import org.junit.Test;

import edu.upc.ichnaea.amqp.model.BuildModels;
import edu.upc.ichnaea.amqp.model.BuildModels.Season;
import edu.upc.ichnaea.amqp.model.Dataset;


public class BuildModelsBuilderTest {

    @Test
    public void testXML() throws ParserConfigurationException
    {    
    	Dataset dataset = new Dataset();
        List<Float> column = new ArrayList<Float>();
        column.add(1f);
        column.add(2f);
        column.add(3f);
        dataset.setColumn("test", column);
        
        List<Float> column2 = new ArrayList<Float>();
        column2.add(4f);
        column2.add(5f);
        column2.add(6f);
        dataset.setColumn("test2", column);
        
        BuildModels model = new BuildModels(dataset, Season.Summer);
        
        String xml = new BuildModelsBuilder().build(model).toString();
        assertEquals("<message season=\"summer\" type=\"build_models\"><dataset><column name=\"test\"><value>1.0</value><value>2.0</value><value>3.0</value></column><column name=\"test2\"><value>1.0</value><value>2.0</value><value>3.0</value></column></dataset></message>", xml);
    }
}
