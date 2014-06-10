

/**
 * GndRequester.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.4.1  Built on : Aug 13, 2008 (05:03:35 LKT)
 */

    package de.qterra.gnd.services;

    /*
     *  GndRequester java interface
     */

    public interface GndRequester {
          

        /**
          * Auto generated method signature
          * An Operation that requests URIs and other information (if any) provided by the GND from given first and last name of a person
                    * @param getGndPersonInfo0
                
         */

         
                     public de.qterra.gnd.webservice.GetGndPersonInfoResponse getGndPersonInfo(

                        de.qterra.gnd.webservice.GetGndPersonInfo getGndPersonInfo0)
                        throws java.rmi.RemoteException
             ;

        
         /**
            * Auto generated method signature for Asynchronous Invocations
            * An Operation that requests URIs and other information (if any) provided by the GND from given first and last name of a person
                * @param getGndPersonInfo0
            
          */
        public void startgetGndPersonInfo(

            de.qterra.gnd.webservice.GetGndPersonInfo getGndPersonInfo0,

            final de.qterra.gnd.services.GndRequesterCallbackHandler callback)

            throws java.rmi.RemoteException;

     

        /**
          * Auto generated method signature
          * 
                    * @param getResourcesByIdentifier2
                
         */

         
                     public de.qterra.gnd.services.GetResourcesByIdentifierResponse getResourcesByIdentifier(

                        de.qterra.gnd.services.GetResourcesByIdentifier getResourcesByIdentifier2)
                        throws java.rmi.RemoteException
             ;

        
         /**
            * Auto generated method signature for Asynchronous Invocations
            * 
                * @param getResourcesByIdentifier2
            
          */
        public void startgetResourcesByIdentifier(

            de.qterra.gnd.services.GetResourcesByIdentifier getResourcesByIdentifier2,

            final de.qterra.gnd.services.GndRequesterCallbackHandler callback)

            throws java.rmi.RemoteException;

     

        /**
          * Auto generated method signature
          * 
                    * @param getResourcesByPnd4
                
         */

         
                     public de.qterra.gnd.webservice.GetResourcesByPndResponse getResourcesByPnd(

                        de.qterra.gnd.webservice.GetResourcesByPnd getResourcesByPnd4)
                        throws java.rmi.RemoteException
             ;

        
         /**
            * Auto generated method signature for Asynchronous Invocations
            * 
                * @param getResourcesByPnd4
            
          */
        public void startgetResourcesByPnd(

            de.qterra.gnd.webservice.GetResourcesByPnd getResourcesByPnd4,

            final de.qterra.gnd.services.GndRequesterCallbackHandler callback)

            throws java.rmi.RemoteException;

     

        
       //
       }
    