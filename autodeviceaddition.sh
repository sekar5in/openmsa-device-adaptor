#!/bin/bash

# This is script to Add the New device in GUI of Ubiqube OpenMSA
# Copyrights Reserved (c)
# ContecUAE
# i2i Telesource india Pvt Ltd.
#
# Author        :       Dhanasekara Pandian
# Email         :       dhana.s@contecuae.com
# Date          :       09NOV2018
#
###

##GLOBAL VARIABLES
DATE=`date +%Y%m%d`
LOGFILE="/tmp/deviceaddition.log"
LOG1="INFO: MANUFACTURER: "
LOG2="INFO: MODEL: "
LOG3="INFO: MODEL IDENTIFIER: "
LOG4="INFO: MODEL FEATURES: "
LOG5="INFO: MANUFACTURER GUI: "
LOG6="INFO: MANUFACTURER REPOSITORY: "

addManufacturer()
{
        ##LOCAL VARIABLES
        manufacturers_properties=/opt/ubi-jentreprise/resources/templates/conf/device/manufacturers.properties
        ManufacturerName=$1

        ## ADD Manufacturer
        echo "$LOG1 Adding New Device Manufacturer" >> $LOGFILE

        #Finding the last manufacturer in the properties file.
        LAST_MANUFACTURE_SEQUENCE=`awk 'END{print}' $manufacturers_properties | awk -F"," '{print $1}'`
        echo "$LOG1 LAST_MANUFACTURE_SEQUENCE is $LAST_MANUFACTURE_SEQUENCE" >> $LOGFILE

        #Finding the next manufacturer sequence number
        NEXT_MANUFACTURE_SEQUENCE=`expr $LAST_MANUFACTURE_SEQUENCE + 1`
        echo "$LOG1 NEXT_MANUFACTURE_SEQUENCE is $NEXT_MANUFACTURE_SEQUENCE" >> $LOGFILE

        #Adding the new manufacturer into the properties file
        cp $manufacturers_properties $manufacturers_properties"_$DATE"
        echo "$LOG1 manufacturers_properties has backup as manufacturers_properties_$DATE" >> $LOGFILE

        ## Format : <ManufacturerID>,<ManufacturerName>,<isSupported> ##
        ## Example : 17010302,"VEEX", $UBI_VSOC_SUPPORT_VEEX_DEVICE ##
        echo -e "\n$NEXT_MANUFACTURE_SEQUENCE,\"$ManufacturerName\",1" >> $manufacturers_properties
        echo "$LOG1 NEXT_MANUFACTURE_SEQUENCE  $NEXT_MANUFACTURE_SEQUENCE is Added successfully" >> $LOGFILE

        NEW_MANUFACTURER=`tail -n1 $manufacturers_properties`
        echo "$LOG1 $NEW_MANUFACTURER" >> $LOGFILE

}


addModel()
{
        ##LOCAL VARIABLES
        model_properties="/opt/ubi-jentreprise/resources/templates/conf/device/models.properties"
        ModelName=$1
        Type=$2

        ## New Model Addition
        echo "$LOG2 Adding New Device Model" >> $LOGFILE

        #Finding the last model in the properties file.
        LAST_MODEL_SEQUENCE=`awk 'END{print}' $model_properties | awk -F"," '{print $1}'`
        echo "$LOG2 LAST_MODEL_SEQUENCE is $LAST_MODEL_SEQUENCE" >> $LOGFILE

        #Finding the next model sequence number
        NEXT_MODEL_SEQUENCE=`expr $LAST_MODEL_SEQUENCE + 1`
        echo "$LOG2 NEXT_MODEL_SEQUENCE is $NEXT_MODEL_SEQUENCE" >> $LOGFILE

        #Adding the new model into the properties file
        cp $model_properties $model_properties"_$DATE"
        echo "$LOG2 model_properties has backup as model_properties_$DATE" >> $LOGFILE

        ##FORMAT : <ModelID>,<ManufacturerID>,<ModeleName>,<type>,<obsolete>,<starcenterEnabled>,<familyId>,<managed>,<utm>,<proxy>,<wizard>,<oec>,<category>,<detailedReportMail>,<detailedReportFirewall>,<detailedReportVpn> ##
        ##Example : 16010402,25,"White Box","H",1,0,1,0,0,1,0,0,0,0,0,SR,0,0 ##
        echo -e "\n$NEXT_MODEL_SEQUENCE,$NEXT_MANUFACTURE_SEQUENCE,\"$ModelName\",\"$Type\",0,1,0,1,0,0,1,0,U,0,0,0" >> $model_properties
        echo "$LOG2 NEXT_MODEL_SEQUENCE  $NEXT_MODEL_SEQUENCE is Added successfully" >> $LOGFILE

        NEW_MODEL=`tail -n1 $model_properties`
        echo "$LOG2 $NEW_MODEL" >> $LOGFILE

}


modelIdentifier()
{
        ##LOCAL VARIABLES
        src_sdExtendedInfo_properties="/opt/ses/templates/server_ALL/sdExtendedInfo.properties"
        tgt_sdExtendedInfo_properties="/opt/ses/properties/specifics/server_ALL/sdExtendedInfo.properties"

        ## Update Model Identifier
        echo "$LOG3 Updating Model Identifier" >> $LOGFILE

        #Backup Model Identifier Properties file
        if [ ! -f $tgt_sdExtendedInfo_properties ]; then
                cp $src_sdExtendedInfo_properties $tgt_sdExtendedInfo_properties
                echo "$LOG3 sdExtendedInfo_properties has copied as sdExtendedInfo_properties" >> $LOGFILE
        else
                cp $tgt_sdExtendedInfo_properties "$tgt_sdExtendedInfo_properties_$DATE"
                echo "$LOG3 tgt_sdExtendedInfo_properties has backup as tgt_sdExtendedInfo_properties_$DATE" >> $LOGFILE
        fi

        ##syntax
        ## Manufacturer name
        ##sdExtendedInfo.router.<ManufacturerID>-<ModelID> = <modelIdentifier>
        ## Manufacturer name
        ##sdExtendedInfo.jspType.<ManufacturerID>-<ModelID> = <modelIdentifier>

        echo -e "\n## $ManufacturerName" >>$tgt_sdExtendedInfo_properties
        echo -e "sdExtendedInfo.router.$NEXT_MANUFACTURE_SEQUENCE-$NEXT_MODEL_SEQUENCE = $ModelName \n" >>$tgt_sdExtendedInfo_properties
        echo -e "## $ManufacturerName" >>$tgt_sdExtendedInfo_properties
        echo -e "sdExtendedInfo.jspType.$NEXT_MANUFACTURE_SEQUENCE-$NEXT_MODEL_SEQUENCE = $ModelName \n" >>$tgt_sdExtendedInfo_properties
        echo "$LOG3 sdExtendedInfo_properties has updated successfully" >> $LOGFILE

}


modelFeatures()
{
        ##LOCAL VARIABLES
        src_manageLinks_properties="/opt/ses/templates/server_ALL/manageLinks.properties"
        tgt_manageLinks_properties="/opt/ses/properties/specifics/server_ALL/manageLinks.properties"

        ## Update Model Features
        echo "$LOG4 Updating Model Features" >> $LOGFILE

        #Backup Model Features Properties file
        if [ ! -f $tgt_manageLinks_properties ]; then
                cp $src_manageLinks_properties $tgt_manageLinks_properties
                echo "$LOG4 manageLinks_properties has copied as manageLinks_properties" >> $LOGFILE
        else
                cp $tgt_manageLinks_properties "$tgt_manageLinks_properties_$DATE"
                echo "$LOG4 tgt_manageLinks_properties has backup as tgt_manageLinks_properties_$DATE" >> $LOGFILE
        fi

        ## Format :
        ## siteLink.initialProv.models= <modelIdentifier> ciscoCatalystIOS pix63Pix63 psgv100wPsgv100w vmwareHost vmwareVM ciscoSA500
        ## device.wizard.automatical.update.models = <modelIdentifier> ciscoUC500 ciscoUC320 ciscoSW300

        RETRIEVE_SITELINK_INITIALPROV_MODELS=`cat $tgt_manageLinks_properties | grep siteLink.initialProv.models`
        RETRIEVE_DEVICE_WIZARD_AUTOMATICAL_UPDATE_MODELS=`cat  $tgt_manageLinks_properties | grep device.wizard.automatical.update.models`

    # siteLink.initialProv.models parameter in place update in file
        sed -i -e "s/$RETRIEVE_SITELINK_INITIALPROV_MODELS/$RETRIEVE_SITELINK_INITIALPROV_MODELS $ModelName/g" $tgt_manageLinks_properties
        echo "$LOG4 siteLink.initialProv.models has updated successfully" >> $LOGFILE

        # device.wizard.automatical.update.models parameter in place update in file
        sed -i -e "s/$RETRIEVE_DEVICE_WIZARD_AUTOMATICAL_UPDATE_MODELS/$RETRIEVE_DEVICE_WIZARD_AUTOMATICAL_UPDATE_MODELS $ModelName/g" $tgt_manageLinks_properties
        echo "$LOG4 device.wizard.automatical.update.models has updated successfully" >> $LOGFILE

        # Job completed successfully
        echo "$LOG4 Model features has updated successfully" >> $LOGFILE

}

addManufacturerGUI()
{
        ##LOCAL VARIABLES
        src_ses_properties="/opt/ses/templates/server_ALL/ses.properties"
        tgt_ses_properties="/opt/ses/properties/specifics/server_ALL/ses.properties"

        ## Add Manufacturer for GUI
        echo "$LOG5 Adding Manufacturer for GUI Mode" >> $LOGFILE

        #Backup ses Properties file
        if [ ! -f $tgt_ses_properties ]; then
                cp $src_ses_properties $tgt_ses_properties
                echo "$LOG5 Templates ses_properties has copied as specifics ses_properties" >> $LOGFILE
        else
                cp $tgt_ses_properties "$tgt_ses_properties_$DATE"
                echo "$LOG5 ses_properties has backup as ses_properties_$DATE" >> $LOGFILE
        fi

        ##Format : soc.device.supported.<newMan_toLowerCase>=1
        ## NOTE : manufacturer name must be in lower case.

        #ModelNamme Lower Case converter
        LOWER_MANUFACTURER=`echo $ManufacturerName | tr '[:upper:]' '[:lower:]'`
        echo "$LOG5 ModelName converted to Lower case $LOWER_MANUFACTURER" >> $LOGFILE
        #Update ses properties file with new model
        echo -e "\nsoc.device.supported.$LOWER_MANUFACTURER=1" >>$tgt_ses_properties
        echo "$LOG5 ses_properties has updated successfully" >> $LOGFILE
}


addManufacturerRepository()
{
        ##LOCAL VARIABLES
        src_repository_properties="/opt/ses/templates/server_ALL/repository.properties"
        tgt_repository_properties="/opt/ses/properties/specifics/server_ALL/repository.properties"

        ##Add New Manufacturer Repository
        echo "$LOG6 Adding New Manufacturer Repository" >> $LOGFILE

        #Backup Repository Properties file
        if [ ! -f $tgt_repository_properties ]; then
                cp $src_repository_properties $tgt_repository_properties
                echo "$LOG6 Templates repository.properties has copied as specifics repository.properties" >> $LOGFILE
        else
                cp $tgt_repository_properties "$tgt_repository_properties_$DATE"
                echo "$LOG6 repository.properties has backup as repository.properties_$DATE" >> $LOGFILE
        fi

        ## Format :
        ## repository.manufacturer= NETASQ CISCO JUNIPER FORTINET VMWARE ONEACCESS <newMan_toUpperCase> BLUECOAT
        ## NOTE : Manufacturer name must be in upper case.

        RETRIEVE_REPOSITORY_MANUFACTURER=`cat $tgt_repository_properties | grep ^repository.manufacturer`

        #Manufacturer Upper Case converter
        UPPER_MANUFACTURER=`echo $ManufacturerName | tr '[:lower:]' '[:upper:]' `

    # new manufacturer repository parameter update in file
        sed -i -e "s/$RETRIEVE_REPOSITORY_MANUFACTURER/$RETRIEVE_REPOSITORY_MANUFACTURER $UPPER_MANUFACTURER/g" $tgt_repository_properties
        echo "$LOG6 New $ManufacturerName repository has updated successfully" >> $LOGFILE

        ##Format : repository.model.<newMan>=<ManufacturerID>-<ModelID>
        echo -e "\nrepository.model.$ManufacturerName=$NEXT_MANUFACTURE_SEQUENCE-$NEXT_MODEL_SEQUENCE" >>$tgt_repository_properties
        echo "$LOG6 New repository model has updated successfully" >> $LOGFILE

        ##Format : repository.access.<newMan_toLowerCase>=|<feature1>|<feature2>|<...>
        ENABLED_FEATURES="|Configuration|Firmware|CommandDefinition|Datafiles|Reports|License|Documentation|Ticketing|Orchestration|Process|"
        LOWER_MANUFACTURER=`echo $ManufacturerName | tr '[:upper:]' '[:lower:]'`
        echo -e "\nrepository.access.$LOWER_MANUFACTURER=$ENABLED_FEATURES" >>$tgt_repository_properties
        echo "$LOG6 repository access has updated successfully" >> $LOGFILE

        # Job completed successfully
        echo "$LOG6 $ManufacturerName Repository has added successfully" >> $LOGFILE

}



## MAIN PROGRAM STARTS ####
## MANUFACTURER : systrome
## Model :	sx600h

addManufacturer systrome
addModel sx600h H
modelIdentifier
modelFeatures
addManufacturerGUI
addManufacturerRepository

## MAIN PROGRAM END ###