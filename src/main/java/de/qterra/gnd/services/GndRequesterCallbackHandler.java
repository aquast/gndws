
/**
 * GndRequesterCallbackHandler.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis2 version: 1.5.1  Built on : Oct 19, 2009 (10:59:00 EDT)
 */

    package de.qterra.gnd.services;

    /**
     *  GndRequesterCallbackHandler Callback class, Users can extend this class and implement
     *  their own receiveResult and receiveError methods.
     */
    public abstract class GndRequesterCallbackHandler{



    protected Object clientData;

    /**
    * User can pass in any object that needs to be accessed once the NonBlocking
    * Web service call is finished and appropriate method of this CallBack is called.
    * @param clientData Object mechanism by which the user can pass in user data
    * that will be avilable at the time this callback is called.
    */
    public GndRequesterCallbackHandler(Object clientData){
        this.clientData = clientData;
    }

    /**
    * Please use this constructor if you don't want to set any clientData
    */
    public GndRequesterCallbackHandler(){
        this.clientData = null;
    }

    /**
     * Get the client data
     */

     public Object getClientData() {
        return clientData;
     }

        
           /**
            * auto generated Axis2 call back method for getGndPersonInfo method
            * override this method for handling normal response from getGndPersonInfo operation
            */
           public void receiveResultgetGndPersonInfo(
                    de.qterra.gnd.webservice.GetGndPersonInfoResponse result
                        ) {
           }

          /**
           * auto generated Axis2 Error handler
           * override this method for handling error response from getGndPersonInfo operation
           */
            public void receiveErrorgetGndPersonInfo(java.lang.Exception e) {
            }
                
           /**
            * auto generated Axis2 call back method for getResourcesByPnd method
            * override this method for handling normal response from getResourcesByPnd operation
            */
           public void receiveResultgetResourcesByPnd(
                    de.qterra.gnd.webservice.GetResourcesByPndResponse result
                        ) {
           }

          /**
           * auto generated Axis2 Error handler
           * override this method for handling error response from getResourcesByPnd operation
           */
            public void receiveErrorgetResourcesByPnd(java.lang.Exception e) {
            }
                


    }
    