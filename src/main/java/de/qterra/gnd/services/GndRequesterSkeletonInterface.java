
/**
 * GndRequesterSkeletonInterface.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5  Built on : Apr 30, 2009 (06:07:24 EDT)
 */
    package de.qterra.gnd.services;
    /**
     *  GndRequesterSkeletonInterface java skeleton interface for the axisService
     */
    public interface GndRequesterSkeletonInterface {
     
         
        /**
         * Auto generated method signature
         * 
                                    * @param getPublicationsByCreatorName
         */

        
                public de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse getPublicationsByCreatorName
                (
                  de.qterra.gnd.webservice.GetPublicationsByCreatorName getPublicationsByCreatorName
                 )
            ;
        
         
        /**
         * Auto generated method signature
         * 
                                    * @param getGndKeyword
         */

        
                public de.qterra.gnd.webservice.GetGndKeywordResponse getGndKeyword
                (
                  de.qterra.gnd.webservice.GetGndKeyword getGndKeyword
                 )
            ;
        
         
        /**
         * Auto generated method signature
         * An Operaton that requests URIs and other information (if any) provided by the GND from given first and last name of a person
                                    * @param getGndPersonInfo
         */

        
                public de.qterra.gnd.webservice.GetGndPersonInfoResponse getGndPersonInfo
                (
                  de.qterra.gnd.webservice.GetGndPersonInfo getGndPersonInfo
                 )
            ;
        
         }
    