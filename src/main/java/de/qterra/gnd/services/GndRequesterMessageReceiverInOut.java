
/**
 * GndRequesterMessageReceiverInOut.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5.1  Built on : Oct 19, 2009 (10:59:00 EDT)
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

        

            if("getGndPersonInfo".equals(methodName)){
                
                de.qterra.gnd.webservice.GetGndPersonInfoResponse getGndPersonInfoResponse1 = null;
	                        de.qterra.gnd.webservice.GetGndPersonInfo wrappedParam =
                                                             (de.qterra.gnd.webservice.GetGndPersonInfo)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.webservice.GetGndPersonInfo.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getGndPersonInfoResponse1 =
                                                   
                                                   
                                                         skel.getGndPersonInfo(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getGndPersonInfoResponse1, false);
                                    } else 

            if("getResourcesByIdentifier".equals(methodName)){
                
                de.qterra.gnd.services.GetResourcesByIdentifierResponse getResourcesByIdentifierResponse3 = null;
	                        de.qterra.gnd.services.GetResourcesByIdentifier wrappedParam =
                                                             (de.qterra.gnd.services.GetResourcesByIdentifier)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.services.GetResourcesByIdentifier.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getResourcesByIdentifierResponse3 =
                                                   
                                                   
                                                         skel.getResourcesByIdentifier(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getResourcesByIdentifierResponse3, false);
                                    } else 

            if("getResourcesByPnd".equals(methodName)){
                
                de.qterra.gnd.webservice.GetResourcesByPndResponse getResourcesByPndResponse5 = null;
	                        de.qterra.gnd.webservice.GetResourcesByPnd wrappedParam =
                                                             (de.qterra.gnd.webservice.GetResourcesByPnd)fromOM(
                                    msgContext.getEnvelope().getBody().getFirstElement(),
                                    de.qterra.gnd.webservice.GetResourcesByPnd.class,
                                    getEnvelopeNamespaces(msgContext.getEnvelope()));
                                                
                                               getResourcesByPndResponse5 =
                                                   
                                                   
                                                         skel.getResourcesByPnd(wrappedParam)
                                                    ;
                                            
                                        envelope = toEnvelope(getSOAPFactory(msgContext), getResourcesByPndResponse5, false);
                                    
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
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.services.GetResourcesByIdentifier param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.services.GetResourcesByIdentifier.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.services.GetResourcesByIdentifierResponse param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.services.GetResourcesByIdentifierResponse.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetResourcesByPnd param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetResourcesByPnd.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

            }
        
            private  org.apache.axiom.om.OMElement  toOM(de.qterra.gnd.webservice.GetResourcesByPndResponse param, boolean optimizeContent)
            throws org.apache.axis2.AxisFault {

            
                        try{
                             return param.getOMElement(de.qterra.gnd.webservice.GetResourcesByPndResponse.MY_QNAME,
                                          org.apache.axiom.om.OMAbstractFactory.getOMFactory());
                        } catch(org.apache.axis2.databinding.ADBException e){
                            throw org.apache.axis2.AxisFault.makeFault(e);
                        }
                    

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
                    
                    private  org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory, de.qterra.gnd.services.GetResourcesByIdentifierResponse param, boolean optimizeContent)
                        throws org.apache.axis2.AxisFault{
                      try{
                          org.apache.axiom.soap.SOAPEnvelope emptyEnvelope = factory.getDefaultEnvelope();
                           
                                    emptyEnvelope.getBody().addChild(param.getOMElement(de.qterra.gnd.services.GetResourcesByIdentifierResponse.MY_QNAME,factory));
                                

                         return emptyEnvelope;
                    } catch(org.apache.axis2.databinding.ADBException e){
                        throw org.apache.axis2.AxisFault.makeFault(e);
                    }
                    }
                    
                         private de.qterra.gnd.services.GetResourcesByIdentifierResponse wrapgetResourcesByIdentifier(){
                                de.qterra.gnd.services.GetResourcesByIdentifierResponse wrappedElement = new de.qterra.gnd.services.GetResourcesByIdentifierResponse();
                                return wrappedElement;
                         }
                    
                    private  org.apache.axiom.soap.SOAPEnvelope toEnvelope(org.apache.axiom.soap.SOAPFactory factory, de.qterra.gnd.webservice.GetResourcesByPndResponse param, boolean optimizeContent)
                        throws org.apache.axis2.AxisFault{
                      try{
                          org.apache.axiom.soap.SOAPEnvelope emptyEnvelope = factory.getDefaultEnvelope();
                           
                                    emptyEnvelope.getBody().addChild(param.getOMElement(de.qterra.gnd.webservice.GetResourcesByPndResponse.MY_QNAME,factory));
                                

                         return emptyEnvelope;
                    } catch(org.apache.axis2.databinding.ADBException e){
                        throw org.apache.axis2.AxisFault.makeFault(e);
                    }
                    }
                    
                         private de.qterra.gnd.webservice.GetResourcesByPndResponse wrapgetResourcesByPnd(){
                                de.qterra.gnd.webservice.GetResourcesByPndResponse wrappedElement = new de.qterra.gnd.webservice.GetResourcesByPndResponse();
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
        
                if (de.qterra.gnd.webservice.GetGndPersonInfo.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndPersonInfo.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetGndPersonInfoResponse.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetGndPersonInfoResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.services.GetResourcesByIdentifier.class.equals(type)){
                
                           return de.qterra.gnd.services.GetResourcesByIdentifier.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.services.GetResourcesByIdentifierResponse.class.equals(type)){
                
                           return de.qterra.gnd.services.GetResourcesByIdentifierResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetResourcesByPnd.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetResourcesByPnd.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

                }
           
                if (de.qterra.gnd.webservice.GetResourcesByPndResponse.class.equals(type)){
                
                           return de.qterra.gnd.webservice.GetResourcesByPndResponse.Factory.parse(param.getXMLStreamReaderWithoutCaching());
                    

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
    