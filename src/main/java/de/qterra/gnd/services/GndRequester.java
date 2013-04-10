

/**
 * GndRequester.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5.1  Built on : Oct 19, 2009 (10:59:00 EDT)
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
                    * @param getResourcesByPnd2
                
         */

         
                     public de.qterra.gnd.webservice.GetResourcesByPndResponse getResourcesByPnd(

                        de.qterra.gnd.webservice.GetResourcesByPnd getResourcesByPnd2)
                        throws java.rmi.RemoteException
             ;

        
         /**
            * Auto generated method signature for Asynchronous Invocations
            * 
                * @param getResourcesByPnd2
            
          */
        public void startgetResourcesByPnd(

            de.qterra.gnd.webservice.GetResourcesByPnd getResourcesByPnd2,

            final de.qterra.gnd.services.GndRequesterCallbackHandler callback)

            throws java.rmi.RemoteException;

     

        
       //
       }
    