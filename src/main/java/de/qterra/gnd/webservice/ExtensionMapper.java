
/**
 * ExtensionMapper.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5.1  Built on : Oct 19, 2009 (10:59:34 EDT)
 */

            package de.qterra.gnd.webservice;
            /**
            *  ExtensionMapper class
            */
        
        public  class ExtensionMapper{

          public static java.lang.Object getTypeObject(java.lang.String namespaceURI,
                                                       java.lang.String typeName,
                                                       javax.xml.stream.XMLStreamReader reader) throws java.lang.Exception{

              
                  if (
                  "http://gnd.qterra.de/webservice/".equals(namespaceURI) &&
                  "resourceResultType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.ResourceResultType.Factory.parse(reader);
                        

                  }

              
                  if (
                  "http://gnd.qterra.de/webservice/".equals(namespaceURI) &&
                  "personResultType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.PersonResultType.Factory.parse(reader);
                        

                  }

              
             throw new org.apache.axis2.databinding.ADBException("Unsupported type " + namespaceURI + " " + typeName);
          }

        }
    