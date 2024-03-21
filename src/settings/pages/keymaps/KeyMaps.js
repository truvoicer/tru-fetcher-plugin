import React, {useEffect, useState} from 'react';
import {Col, Row, Select, Button, Modal, Card, Space, Form} from 'antd';
import {fetchRequest} from "../../../library/api/state-middleware";
import fetcherApiConfig from "../../../library/api/fetcher-api/fetcherApiConfig";

const Keymaps = () => {

    const [services, setServices] = useState([]);
    const [selectedService, setSelectedService] = useState();
    async function serviceListRequest() {
        const results = await fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });
        console.log(results)
        if (Array.isArray(results?.data?.data?.services)) {
            setServices(results.data.data.services);
        }
    }
    function getServicesOptions() {
        return services.map((item) => {
            return {
                label: item.label,
                value: item.id,
            }
        })
    }

    useEffect(() => {
        serviceListRequest();
    }, []);

    return (
        <>
            <h1>Keymaps</h1>
            <Row>
                <Col>
                    <Form.Item label="Service">
                        {Array.isArray(services) && services.length && (
                            <Select
                                style={{minWidth: 180}}
                                options={getServicesOptions()}
                                value={selectedService}
                                onChange={(e, data) => {
                                    setSelectedService(data.value);
                                }}
                            />
                        )}
                    </Form.Item>
                </Col>
            </Row>
        </>
    );
};

export default Keymaps;
