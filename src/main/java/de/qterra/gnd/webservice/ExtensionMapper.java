
/**
 * ExtensionMapper.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5  Built on : Apr 30, 2009 (06:07:47 EDT)
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
                  "http://gnd.hbz.nrw.de/webservice/".equals(namespaceURI) &&
                  "publResultType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.PublResultType.Factory.parse(reader);
                        

                  }

              
                  if (
                  "http://gnd.hbz.nrw.de/webservice/".equals(namespaceURI) &&
                  "resultType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.ResultType.Factory.parse(reader);
                        

                  }

              
                  if (
                  "http://gnd.hbz.nrw.de/webservice/".equals(namespaceURI) &&
                  "keywordResultType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.KeywordResultType.Factory.parse(reader);
                        

                  }

              
                  if (
                  "http://gnd.hbz.nrw.de/webservice/".equals(namespaceURI) &&
                  "keywordType".equals(typeName)){
                   
                            return  de.qterra.gnd.webservice.KeywordType.Factory.parse(reader);
                        

                  }

              
             throw new org.apache.axis2.databinding.ADBException("Unsupported type " + namespaceURI + " " + typeName);
          }

        }
    