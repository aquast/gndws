
/**
 * GndRequesterMessageReceiverInOut.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5  Built on : Apr 30, 2009 (06:07:24 EDT)
 */
        package de.qterra.gnd.services;

        /**
        *  GndRequesterMessageReceiverInOut message receiver
        */

        public class GndRequesterMessageReceiverInOut extends org.apache.axis2.receivers.AbstractInOutMessageReceiver{


        public void invokeBusinessLogic(org.apache.axis2.context.MessageContext msgContext, org.apache.axis2.context.MessageContext newMsgContext)
        throws org.apache.axis2.AxisFault{

        try {

        // get the implementation class for the Web Service
        Object obj = getTheImplementationObject(msgContext);

        GndRequesterSkeletonInterface skel = (GndRequesterSkeletonInterface)obj;
        //Out Envelop
        org.apache.axiom.soap.SOAPEnvelope envelope = null;
        //Find the axisOperation that has been set by the Dispatch phase.
        org.apache.axis2.description.AxisOperation op = msgContext.getOperationContext().getAxisOperation();
        if (op == null) {
        throw new org.apache.axis2.AxisFault("Operation is not located, if this is doclit style the SOAP-ACTION should specified via the SOAP Action to use the RawXMLProvider");
        }

        java.lang.String methodName;
        if((op.getName() != null) && ((methodName = org.apache.axis2.util.JavaUtils.xmlNameToJavaIdentifier(op.getName().getLocalPart())) != null)){

        

            if("getPublicationsByCreatorName".equals(methodName)){
                
                de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse getPublicationsByCreatorNameResponse7 = null;
	                        de.qterra.gnd.webservice.GetPublicationsByCreatorName wrappedParam =
                                                             (de.qterra.gnd.webservice.GetPublicationsByCreatorName)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.webservice.GetPublicationsByCreatorName.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getPublicationsByCreatorNameResponse7 =
                                                   
                                                   
                                                         skel.getPublicationsByCreatorName(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getPublicationsByCreatorNameResponse7, false);
                                    } else 

            if("getGndKeyword".equals(methodName)){
                
                de.qterra.gnd.webservice.GetGndKeywordResponse getGndKeywordResponse9 = null;
	                        de.qterra.gnd.webservice.GetGndKeyword wrappedParam =
                                                             (de.qterra.gnd.webservice.GetGndKeyword)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.webservice.GetGndKeyword.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getGndKeywordResponse9 =
                                                   
                                                   
                                                         skel.getGndKeyword(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getGndKeywordResponse9, false);
                                    } else 

            if("getGndPersonInfo".equals(methodName)){
                
                de.qterra.gnd.webservice.GetGndPersonInfoResponse getGndPersonInfoResponse11 = null;
	                        de.qterra.gnd.webservice.GetGndPersonInfo wrappedParam =
                                                             (de.qterra.gnd.webservice.GetGndPersonInfo)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.webservice.GetGndPersonInfo.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getGndPersonInfoResponse11 =
                                                   
                                                   
                                                         skel.getGndPersonInfo(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getGndPersonInfoResponse11, false);
                                    
            } else {
              throw new java.lang.RuntimeException("method not found");
            }
        

        newMsgContext.setEnvelope(envelope);
        }
        }
        catch (java.lang.Exception e) {
        throw org.apache.axis2.AxisFault.makeFault(e);
        }
        }
        
        //
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetPublicationsByCreatorName param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetPublicationsByCreatorName.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetGndKeyword param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetGndKeyword.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetGndKeywordResponse param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetGndKeywordResponse.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetGndPersonInfo param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetGndPersonInfo.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetGndPersonInfoResponse param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetGndPersonInfoResponse.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
                    private  org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory, de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse param, boolean optimizeContent)
                        throws org.apache.axis2.AxisFault{
                      try{
                          org.apache.axiom.soap.SOAPEnvelope emptyEnvelope = factory.getDefaultEnvelope();
                           
                                    emptyEnvelope.getBody().addChild(param.getOMElement(de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse.MY_QNAME,factory));
                                

                         return emptyEnvelope;
                    } catch(org.apache.axis2.databinding.ADBException e){
                        throw org.apache.axis2.AxisFault.makeFault(e);
                    }
                    }
                    
                         private de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse wrapgetPublicationsByCreatorName(){
                                de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse wrappedElement = new de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse();
                                return wrappedElement;
                         }
                    
                    private  org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory, de.qterra.gnd.webservice.GetGndKeywordResponse param, boolean optimizeContent)
                        throws org.apache.axis2.AxisFault{
                      try{
                          org.apache.axiom.soap.SOAPEnvelope emptyEnvelope = factory.getDefaultEnvelope();
                           
                                    emptyEnvelope.getBody().addChild(param.getOMElement(de.qterra.gnd.webservice.GetGndKeywordResponse.MY_QNAME,factory));
                                

                         return emptyEnvelope;
                    } catch(org.apache.axis2.databinding.ADBException e){
                        throw org.apache.axis2.AxisFault.makeFault(e);
                    }
                    }
                    
                         private de.qterra.gnd.webservice.GetGndKeywordResponse wrapgetGndKeyword(){
                                de.qterra.gnd.webservice.GetGndKeywordResponse wrappedElement = new de.qterra.gnd.webservice.GetGndKeywordResponse();
                                return wrappedElement;
                         }
                    
                    private  org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory, de.qterra.gnd.webservice.GetGndPersonInfoResponse param, boolean optimizeContent)
                        throws org.apache.axis2.AxisFault{
                      try{
                          org.apache.axiom.soap.SOAPEnvelope emptyEnvelope = factory.getDefaultEnvelope();
                           
                                    emptyEnvelope.getBody().addChild(param.getOMElement(de.qterra.gnd.webservice.GetGndPersonInfoResponse.MY_QNAME,factory));
                                

                         return emptyEnvelope;
                    } catch(org.apache.axis2.databinding.ADBException e){
                        throw org.apache.axis2.AxisFault.makeFault(e);
                    }
                    }
                    
                         private de.qterra.gnd.webservice.GetGndPersonInfoResponse wrapgetGndPersonInfo(){
                                de.qterra.gnd.webservice.GetGndPersonInfoResponse wrappedElement = new de.qterra.gnd.webservice.GetGndPersonInfoResponse();
                                return wrappedElement;
                         }
                    


        /**
        *  get the default envelope
        */
        private org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory){
        return factory.getDefaultEnvelope();
        }


        private  java.lang.Object fromOM(
        org.apache.axiom.om.OMElement param,
        java.lang.Class type,
        java.util.Map extraNamespaces) throws org.apache.axis2.AxisFault{

        try {
        
                if (de.qterra.gnd.webservice.GetPublicationsByCreatorName.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetPublicationsByCreatorName.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetGndKeyword.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndKeyword.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetGndKeywordResponse.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndKeywordResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetGndPersonInfo.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndPersonInfo.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetGndPersonInfoResponse.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndPersonInfoResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
        } catch (java.lang.Exception e) {
        throw org.apache.axis2.AxisFault.makeFault(e);
        }
           return null;
        }



    

        /**
        *  A utility method that copies the namepaces from the SOAPEnvelope
        */
        private java.util.Map getEnvelopeNamespaces(org.apache.axiom.soap.SOAPEnvelope env){
        java.util.Map returnMap = new java.util.HashMap();
        java.util.Iterator namespaceIterator = env.getAllDeclaredNamespaces();
        while (namespaceIterator.hasNext()) {
        org.apache.axiom.om.OMNamespace ns = (org.apache.axiom.om.OMNamespace) namespaceIterator.next();
        returnMap.put(ns.getPrefix(),ns.getNamespaceURI());
        }
        return returnMap;
        }

        private org.apache.axis2.AxisFault createAxisFault(java.lang.Exception e) {
        org.apache.axis2.AxisFault f;
        Throwable cause = e.getCause();
        if (cause != null) {
            f = new org.apache.axis2.AxisFault(e.getMessage(), cause);
        } else {
            f = new org.apache.axis2.AxisFault(e.getMessage());
        }

        return f;
    }

        }//end of class
    