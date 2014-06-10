
/**
 * GndRequesterSkeletonInterface.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.4.1  Built on : Aug 13, 2008 (05:03:35 LKT)
 */
    package de.qterra.gnd.services;
    /**
     *  GndRequesterSkeletonInterface java skeleton interface for the axisService
     */
    public interface GndRequesterSkeletonInterface {
     
         
        /**
         * Auto generated method signature
         * An Operation that requests URIs and other information (if any) provided by the GND from given first and last name of a person
                                    * @param getGndPersonInfo
         */

        
                public de.qterra.gnd.webservice.GetGndPersonInfoResponse getGndPersonInfo
                (
                  de.qterra.gnd.webservice.GetGndPersonInfo getGndPersonInfo
                 )
            ;
        
         
        /**
         * Auto generated method signature
         * 
                                    * @param getResourcesByIdentifier
         */

        
                public de.qterra.gnd.services.GetResourcesByIdentifierResponse getResourcesByIdentifier
                (
                  de.qterra.gnd.services.GetResourcesByIdentifier getResourcesByIdentifier
                 )
            ;
        
         
        /**
         * Auto generated method signature
         * 
                                    * @param getResourcesByPnd
         */

        
                public de.qterra.gnd.webservice.GetResourcesByPndResponse getResourcesByPnd
                (
                  de.qterra.gnd.webservice.GetResourcesByPnd getResourcesByPnd
                 )
            ;
        
         }
    