import React, {useContext} from 'react';
import SettingsContext from "../contexts/SettingsContext";
import Datatable from "../../components/tables/Datatable";

const GeneralSettings = () => {
    const settingsContext = useContext(SettingsContext);
    console.log(settingsContext);
    const columns = [
        {
            title: 'Name',
            dataIndex: 'name',
            width: '30%',
        },
        {
            title: 'Value',
            dataIndex: 'value',
            editable: true,
        },
        // {
        //     title: 'operation',
        //     dataIndex: 'operation',
        //     render: (_, record) =>
        //         dataSource.length >= 1 ? (
        //             <Popconfirm title="Sure to delete?" onConfirm={() => handleDelete(record.key)}>
        //                 <a>Delete</a>
        //             </Popconfirm>
        //         ) : null,
        // },
    ];
    function getSettings() {
        return settingsContext.settings.map((setting, index) => {
            let cloneSetting = {...setting};
            cloneSetting.key = index;
            return cloneSetting
        })
    }
    return (
      <Datatable
          columns={columns}
          dataSource={getSettings()}
          onDelete={({newData, key}) => {
              console.log({newData, key})
          }}
          onSave={({row, col}) => {
              switch (col.dataIndex) {
                    case 'value':
                        if (typeof settingsContext.settings[row.key] !== 'undefined') {
                            let setting = {...settingsContext.settings[row.key]};
                            setting['value'] = row.value;
                            settingsContext.updateSettingByIndex(setting);
                        }
                        break;
              }
          }}
          onAdd={({values}) => {
              settingsContext.addSetting(values)
          }}
      />
    );
};

export default GeneralSettings;
